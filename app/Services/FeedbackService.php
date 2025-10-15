<?php

namespace App\Services;

use App\Exceptions\Feedback\FeedbackAlreadyExistsException;
use App\Models\Feedback;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Order\OrderChecker;
use Illuminate\Support\Carbon;

class FeedbackService
{
    public function __construct(
        protected OrderChecker $orderChecker,
    )
    {}

    public function createFeedback(User $user, OrderItem $orderItem, bool $isPositive = true, ?string $comment = null, ?Carbon $createdAt = null): Feedback
    {
        $this->ensureUniqueFeedback($orderItem->id);
        $this->orderChecker->ensureOrderAccess($orderItem->order, $user);
        $this->orderChecker->ensureCompletedOrder($orderItem->order);

        return Feedback::create([
            'user_id' => $user->id,
            'order_item_id' => $orderItem->id,
            'product_id' => $orderItem->product_id,
            'seller_id' => $orderItem->product->user_id,
            'is_positive' => $isPositive,
            'comment' => $comment ?? null,
            'created_at' => $createdAt ?? Carbon::now(),
        ]);
    }

    public function updateFeedback(Feedback $feedback, bool $isPositive = true, ?string $comment = null): void
    {
        $feedback->is_positive = $isPositive;
        $feedback->comment = $comment;
        $feedback->save();
    }

    public function removeFeedback(Feedback $feedback): void
    {
        $feedback->delete();
    }

    public function ensureUniqueFeedback(int $orderItemId): void
    {
        if ($this->checkExists($orderItemId)) {
            throw new FeedbackAlreadyExistsException();
        }
    }

    public function checkExists(int $orderItemId): bool
    {
        return Feedback::query()->where('order_item_id', $orderItemId)->exists();
    }
}