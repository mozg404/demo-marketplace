<?php

namespace Tests\Feature\Services\Balance;

use App\Enum\TransactionType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Balance\OrderPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderPaymentService $orderPaymentService;
    private User $buyer;
    private User $seller1;
    private User $seller2;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderPaymentService = app(OrderPaymentService::class);

        // Создаем пользователей
        $this->buyer = User::factory()->create(['balance' => 2000]);
        $this->seller1 = User::factory()->create(['balance' => 500]);
        $this->seller2 = User::factory()->create(['balance' => 300]);

        // Создаем заказ
        $this->order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'amount' => 1500,
            'status' => \App\Enum\OrderStatus::PENDING,
        ]);

        // Создаем продукты и связываем с продавцами
        $product1 = Product::factory()->create(['user_id' => $this->seller1->id]);
        $product2 = Product::factory()->create(['user_id' => $this->seller2->id]);

        $stockItem1 = StockItem::factory()->create(['product_id' => $product1->id]);
        $stockItem2 = StockItem::factory()->create(['product_id' => $product2->id]);

        // Создаем позиции заказа
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product1->id,
            'stock_item_id' => $stockItem1->id,
            'current_price' => 800,
            'base_price' => 700,
        ]);

        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product2->id,
            'stock_item_id' => $stockItem2->id,
            'current_price' => 700,
            'base_price' => 600,
        ]);

        // Загружаем связи
        $this->order->load('items.product.user', 'items.stockItem');
    }

    #[Test]
    public function processOrderPaymentWithdrawsMoneyFromBuyer(): void
    {
        $initialBalance = $this->buyer->balance;

        $this->orderPaymentService->processOrderPayment($this->order);

        $this->assertEquals($initialBalance - $this->order->amount, $this->buyer->fresh()->balance);
    }

    #[Test]
    public function processOrderPaymentCreatesTransactionForBuyer(): void
    {
        $this->orderPaymentService->processOrderPayment($this->order);

        $transaction = Transaction::where('user_id', $this->buyer->id)->first();

        $this->assertNotNull($transaction);
        $this->assertEquals(-$this->order->amount, $transaction->amount);
        $this->assertEquals(TransactionType::ORDER_PAYMENT->value, $transaction->type->value);
        $this->assertEquals(Order::class, $transaction->transactionable_type);
        $this->assertEquals($this->order->id, $transaction->transactionable_id);
    }

    #[Test]
    public function processSellerPayoutsDepositsMoneyToSellers(): void
    {
        $initialSeller1Balance = $this->seller1->balance;
        $initialSeller2Balance = $this->seller2->balance;

        $this->orderPaymentService->processSellerPayouts($this->order);

        $seller1Item = $this->order->items->first(fn($item) => $item->product->user_id === $this->seller1->id);
        $seller2Item = $this->order->items->first(fn($item) => $item->product->user_id === $this->seller2->id);

        $this->assertEquals($initialSeller1Balance + $seller1Item->current_price, $this->seller1->fresh()->balance);
        $this->assertEquals($initialSeller2Balance + $seller2Item->current_price, $this->seller2->fresh()->balance);
    }

    #[Test]
    public function processSellerPayoutsCreatesTransactionsForSellers(): void
    {
        $this->orderPaymentService->processSellerPayouts($this->order);

        $transactions = Transaction::whereIn('user_id', [$this->seller1->id, $this->seller2->id])->get();

        $this->assertCount(2, $transactions);

        foreach ($this->order->items as $item) {
            $sellerId = $item->product->user_id;
            $transaction = $transactions->firstWhere('user_id', $sellerId);

            $this->assertNotNull($transaction);
            $this->assertEquals($item->current_price, $transaction->amount);
            $this->assertEquals(TransactionType::SELLER_PAYOUT->value, $transaction->type->value);
            $this->assertEquals(OrderItem::class, $transaction->transactionable_type);
            $this->assertEquals($item->id, $transaction->transactionable_id);
        }
    }

    #[Test]
    public function fullOrderProcessingWorksCorrectly(): void
    {
        $initialBuyerBalance = $this->buyer->balance;
        $initialSeller1Balance = $this->seller1->balance;
        $initialSeller2Balance = $this->seller2->balance;

        $this->orderPaymentService->processOrderPayment($this->order);
        $this->orderPaymentService->processSellerPayouts($this->order);

        $this->assertEquals($initialBuyerBalance - $this->order->amount, $this->buyer->fresh()->balance);

        $seller1Item = $this->order->items->first(fn($item) => $item->product->user_id === $this->seller1->id);
        $seller2Item = $this->order->items->first(fn($item) => $item->product->user_id === $this->seller2->id);

        $this->assertEquals($initialSeller1Balance + $seller1Item->current_price, $this->seller1->fresh()->balance);
        $this->assertEquals($initialSeller2Balance + $seller2Item->current_price, $this->seller2->fresh()->balance);

        $buyerTransaction = Transaction::where('user_id', $this->buyer->id)->first();
        $sellerTransactions = Transaction::whereIn('user_id', [$this->seller1->id, $this->seller2->id])->get();

        $this->assertNotNull($buyerTransaction);
        $this->assertEquals(-$this->order->amount, $buyerTransaction->amount);
        $this->assertCount(2, $sellerTransactions);
    }

    #[Test]
    public function processSellerPayoutsUsesCorrectSellerFromProduct(): void
    {
        $this->orderPaymentService->processSellerPayouts($this->order);

        foreach ($this->order->items as $item) {
            $sellerId = $item->product->user_id;
            $transaction = Transaction::where('user_id', $sellerId)->first();

            $this->assertNotNull($transaction);
            $this->assertEquals($item->current_price, $transaction->amount);
        }
    }

    #[Test]
    public function orderPaymentServiceUsesTransactionableInterface(): void
    {
        $this->orderPaymentService->processOrderPayment($this->order);
        $this->orderPaymentService->processSellerPayouts($this->order);

        $buyerTransaction = Transaction::where('user_id', $this->buyer->id)->first();
        $sellerTransaction = Transaction::where('user_id', $this->seller1->id)->first();

        $this->assertEquals(Order::class, $buyerTransaction->transactionable_type);
        $this->assertEquals($this->order->id, $buyerTransaction->transactionable_id);
        $this->assertEquals(OrderItem::class, $sellerTransaction->transactionable_type);
    }
}