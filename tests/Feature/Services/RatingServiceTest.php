<?php

namespace Tests\Feature\Services;

use App\Models\Feedback;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\RatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RatingServiceTest extends TestCase
{
    use RefreshDatabase;

    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->ratingService = $this->app->make(RatingService::class);
    }

    public function testCalculationRating(): void
    {
        $this->assertEquals(0, $this->ratingService->calculateRating(0, 0));
        $this->assertEquals(0, $this->ratingService->calculateRating(0, 10));
        $this->assertEquals(100, $this->ratingService->calculateRating(1, 0));
        $this->assertEquals(50, $this->ratingService->calculateRating(5, 5));
        $this->assertEquals(75, $this->ratingService->calculateRating(3, 1));
        $this->assertEquals(30, $this->ratingService->calculateRating(3, 7));
    }

    public function testIncreasePositiveRating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for($user)->create();
        $orderItem = OrderItem::factory()->forProduct($product)->create();
        $feedback = Feedback::factory()->isPositive()->forOrderItem($orderItem)->create();

        $this->ratingService->increaseRating($feedback);
        $user = $user->fresh();
        $product = $product->fresh();

        $this->assertEquals(100, $user->seller_rating);
        $this->assertEquals(1, $user->positive_feedbacks_count);
        $this->assertEquals(0, $user->negative_feedbacks_count);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'seller_rating' => 100,
            'positive_feedbacks_count' => 1,
            'negative_feedbacks_count' => 0,
        ]);

        $this->assertEquals(100, $product->rating);
        $this->assertEquals(1, $product->positive_feedbacks_count);
        $this->assertEquals(0, $product->negative_feedbacks_count);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'rating' => 100,
            'positive_feedbacks_count' => 1,
            'negative_feedbacks_count' => 0,
        ]);
    }

    public function testIncreaseNegativeRating(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->for($user)->create();
        $orderItem = OrderItem::factory()->forProduct($product)->create();
        $feedback = Feedback::factory()->isNegative()->forOrderItem($orderItem)->create();

        $this->ratingService->increaseRating($feedback);
        $user = $user->fresh();
        $product = $product->fresh();

        $this->assertEquals(0, $user->seller_rating);
        $this->assertEquals(0, $user->positive_feedbacks_count);
        $this->assertEquals(1, $user->negative_feedbacks_count);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'seller_rating' => 0,
            'positive_feedbacks_count' => 0,
            'negative_feedbacks_count' => 1,
        ]);

        $this->assertEquals(0, $product->rating);
        $this->assertEquals(0, $product->positive_feedbacks_count);
        $this->assertEquals(1, $product->negative_feedbacks_count);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'rating' => 0,
            'positive_feedbacks_count' => 0,
            'negative_feedbacks_count' => 1,
        ]);
    }

    public function testDecreasePositiveRating(): void
    {
        $user = User::factory()->withCalculatedRating(3,2)->create();
        $product = Product::factory()->withCalculatedRating(3,2)->for($user)->create();
        $orderItem = OrderItem::factory()->forProduct($product)->create();
        $feedback = Feedback::factory()->isPositive()->forOrderItem($orderItem)->create();

        $this->ratingService->decreaseRating($feedback);
        $user = $user->fresh();
        $product = $product->fresh();

        $this->assertEquals(50, $user->seller_rating);
        $this->assertEquals(2, $user->positive_feedbacks_count);
        $this->assertEquals(2, $user->negative_feedbacks_count);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'seller_rating' => 50,
            'positive_feedbacks_count' => 2,
            'negative_feedbacks_count' => 2,
        ]);

        $this->assertEquals(50, $product->rating);
        $this->assertEquals(2, $product->positive_feedbacks_count);
        $this->assertEquals(2, $product->negative_feedbacks_count);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'rating' => 50,
            'positive_feedbacks_count' => 2,
            'negative_feedbacks_count' => 2,
        ]);
    }

    public function testDecreaseNegativeRating(): void
    {
        $user = User::factory()->withCalculatedRating(2,3)->create();
        $product = Product::factory()->withCalculatedRating(2,3)->for($user)->create();
        $orderItem = OrderItem::factory()->forProduct($product)->create();
        $feedback = Feedback::factory()->isNegative()->forOrderItem($orderItem)->create();

        $this->ratingService->decreaseRating($feedback);
        $user = $user->fresh();
        $product = $product->fresh();

        $this->assertEquals(50, $user->seller_rating);
        $this->assertEquals(2, $user->positive_feedbacks_count);
        $this->assertEquals(2, $user->negative_feedbacks_count);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'seller_rating' => 50,
            'positive_feedbacks_count' => 2,
            'negative_feedbacks_count' => 2,
        ]);

        $this->assertEquals(50, $product->rating);
        $this->assertEquals(2, $product->positive_feedbacks_count);
        $this->assertEquals(2, $product->negative_feedbacks_count);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'rating' => 50,
            'positive_feedbacks_count' => 2,
            'negative_feedbacks_count' => 2,
        ]);
    }

    public function testPositiveToNegativeChangeRating(): void
    {
        $user = User::factory()->withCalculatedRating(3,2)->create();
        $product = Product::factory()->withCalculatedRating(3,2)->for($user)->create();
        $orderItem = OrderItem::factory()->forProduct($product)->create();
        $feedback = Feedback::factory()->forOrderItem($orderItem)->create();

        // Создаем сущность
        $this->ratingService->changeRating($feedback);
        $user = $user->fresh();
        $product = $product->fresh();

        $this->assertEquals(40, $user->seller_rating);
        $this->assertEquals(2, $user->positive_feedbacks_count);
        $this->assertEquals(3, $user->negative_feedbacks_count);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'seller_rating' => 40,
            'positive_feedbacks_count' => 2,
            'negative_feedbacks_count' => 3,
        ]);

        $this->assertEquals(40, $product->rating);
        $this->assertEquals(2, $product->positive_feedbacks_count);
        $this->assertEquals(3, $product->negative_feedbacks_count);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'rating' => 40,
            'positive_feedbacks_count' => 2,
            'negative_feedbacks_count' => 3,
        ]);
    }

    public function testNegativeToPositiveChangeRating(): void
    {
        $user = User::factory()->withCalculatedRating(2,3)->create();
        $product = Product::factory()->withCalculatedRating(2,3)->for($user)->create();
        $orderItem = OrderItem::factory()->forProduct($product)->create();
        $feedback = Feedback::factory()->forOrderItem($orderItem)->create();

        $this->ratingService->changeRating($feedback, false);
        $user = $user->fresh();
        $product = $product->fresh();

        $this->assertEquals(60, $user->seller_rating);
        $this->assertEquals(3, $user->positive_feedbacks_count);
        $this->assertEquals(2, $user->negative_feedbacks_count);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'seller_rating' => 60,
            'positive_feedbacks_count' => 3,
            'negative_feedbacks_count' => 2,
        ]);

        $this->assertEquals(60, $product->rating);
        $this->assertEquals(3, $product->positive_feedbacks_count);
        $this->assertEquals(2, $product->negative_feedbacks_count);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'rating' => 60,
            'positive_feedbacks_count' => 3,
            'negative_feedbacks_count' => 2,
        ]);
    }
}
