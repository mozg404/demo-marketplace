<?php

namespace Tests\Feature\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\User;
use App\Services\FeedbackService;
use App\Services\Order\OrderChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackServiceTest extends TestCase
{
    use RefreshDatabase;

    private FeedbackService $feedbackService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackService = $this->app->make(FeedbackService::class);
    }

    public function testCorrectCreation(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $stockItem1 = StockItem::factory()->for($product)->create();
        $order = Order::factory()->completed()->for($user)->create();
        $orderItem = OrderItem::factory()->forStockItem($stockItem1)->for($order)->create();
        $orderCheckerMock = $this->createMock(OrderChecker::class);
        $comment = 'Test comment';

        $orderCheckerMock->expects($this->once())
            ->method('ensureOrderAccess')
            ->with($orderItem->order, $user);
        $orderCheckerMock->expects($this->once())
            ->method('ensureCompletedOrder')
            ->with($orderItem->order);

        $feedbackService = new FeedbackService($orderCheckerMock);

        $feedback = $feedbackService->createFeedback($user, $orderItem, false, $comment);

        $this->assertEquals($user->id, $feedback->user_id);
        $this->assertEquals($product->id, $feedback->product_id);
        $this->assertEquals($orderItem->id, $feedback->order_item_id);
        $this->assertEquals($product->user_id, $feedback->seller_id);
        $this->assertFalse($feedback->is_positive);
        $this->assertEquals($comment, $feedback->comment);
        $this->assertDatabaseHas('feedbacks', [
            'user_id' => $user->id,
            'order_item_id' => $orderItem->id,
            'product_id' => $product->id,
            'seller_id' => $product->user_id,
            'is_positive' => false,
            'comment' => $comment,
        ]);
    }

    public function testCreateWithEmptyComment(): void
    {
        $stockItem1 = StockItem::factory()->create();
        $order = Order::factory()->completed()->create();
        $orderItem = OrderItem::factory()->forStockItem($stockItem1)->for($order)->create();

        $feedback = $this->feedbackService->createFeedback($order->user, $orderItem);

        $this->assertNull($feedback->comment);
        $this->assertDatabaseHas('feedbacks', [
            'order_item_id' => $orderItem->id,
            'is_positive' => true,
            'comment' => null,
        ]);
    }
}
