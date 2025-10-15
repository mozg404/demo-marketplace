<?php

namespace Tests\Feature\Services\Payment;

use App\Enum\OrderStatus;
use App\Enum\PaymentSource;
use App\Enum\PaymentStatus;
use App\Enum\TransactionType;
use App\Exceptions\Payment\PaymentAlreadyCompletedException;
use App\Exceptions\Payment\PaymentCancelledException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentGateway\PaymentProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentProcessorTest extends TestCase
{
    use RefreshDatabase;

    private PaymentProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->processor = $this->app->make(PaymentProcessor::class);
    }

    public function testPaymentAlreadyCompleted()
    {
        $payment = Payment::factory()->make(['status' => PaymentStatus::COMPLETED->value]);
        $this->expectException(PaymentAlreadyCompletedException::class);
        $this->processor->process($payment);
    }

    public function testPaymentCancelled()
    {
        $payment = Payment::factory()->make(['status' => PaymentStatus::CANCELLED->value]);
        $this->expectException(PaymentCancelledException::class);
        $this->processor->process($payment);
    }

    public function testPaymentFailed()
    {
        $payment = Payment::factory()->make(['status' => PaymentStatus::FAILED->value]);
        $this->expectException(PaymentFailedException::class);
        $this->processor->process($payment);
    }

    public function testCompletedReplenishmentPayment()
    {
        $payment = Payment::factory()->forReplenishment()->create([
            'amount' => 2000,
            'status' => PaymentStatus::SUCCESS,
        ]);

        $payment = $this->processor->process($payment);

        // Проверяем, что статус COMPLETED
        $this->assertEquals(PaymentStatus::COMPLETED, $payment->status);

        // Проверяем, что у пользователя на балансе сумма пополнения
        $this->assertDatabaseHas('users', [
            'id' => $payment->user_id,
            'balance' => 2000,
        ]);

        // Есть транзакция с указанием суммы и платежа
        $this->assertDatabaseHas('transactions', [
            'user_id' => $payment->user_id,
            'amount' => 2000,
            'type' => TransactionType::GATEWAY_DEPOSIT,
            'transactionable_id' => $payment->id,
        ]);
    }

    public function testCompletedOrderPayment()
    {
        $order = Order::factory()->pending()->withItems()->create();
        $payment = Payment::factory()->forOrder($order)->create([
            'status' => PaymentStatus::SUCCESS,
        ]);

        $payment = $this->processor->process($payment);

        // Платеж в бд со статусом "Оплачен"
        $this->assertDatabaseHas('orders', [
            'id' => $payment->sourceable->id,
            'status' => OrderStatus::COMPLETED,
        ]);

        // Транзакция с указанием типа оплаты заказа И id заказа
        $this->assertDatabaseHas('transactions', [
            'user_id' => $payment->user_id,
            'type' => TransactionType::ORDER_PAYMENT,
            'transactionable_id' => $payment->sourceable->id,
        ]);

        // У пользователя должен быть нулевой баланс
        $this->assertDatabaseHas('users', [
            'id' => $payment->user_id,
            'balance' => 0,
        ]);

        // Статус у модели COMPLETED
        $this->assertEquals(PaymentStatus::COMPLETED, $payment->status);
    }
}
