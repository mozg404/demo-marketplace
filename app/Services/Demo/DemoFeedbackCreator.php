<?php

namespace App\Services\Demo;

use App\Models\Feedback;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\FeedbackService;
use Illuminate\Support\Carbon;

readonly class DemoFeedbackCreator
{
    public function __construct(
        private FeedbackService $feedbackService,
    ) {
    }

    public function createForOrder(Order $order): void
    {
        $order->items->each(function (OrderItem $item) use ($order) {
            $this->create($order->user, $item);
        });
    }

    public function create(User $user, OrderItem $item): Feedback
    {
        $isPositive = fake()->boolean();

        return $this->feedbackService->createFeedback(
            user: $user,
            orderItem: $item,
            isPositive: $isPositive,
            comment: fake()->randomElement(match ($isPositive) {
                false => include resource_path('data/negative_feedback_comments.php'),
                true => include resource_path('data/positive_feedback_comments.php'),
            }),
            createdAt: new Carbon(fake()->dateTimeBetween('-1 year'))
        );
    }
}