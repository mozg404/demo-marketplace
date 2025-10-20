<?php

namespace App\Services\Rating;

use App\Models\Feedback;
use App\Models\Product;
use App\Models\User;
use App\Support\RatingCalculator;
use Illuminate\Support\Facades\DB;

class RatingService
{
    public function increaseRating(Feedback $feedback): void
    {
        DB::transaction(function () use ($feedback) {
            $this->changeProductCounter($feedback->product, true, $feedback->is_positive);
            $this->recalculateProductRatingByCounters($feedback->product);
            $this->changeSellerCounter($feedback->seller, true, $feedback->is_positive);
            $this->recalculateSellerRatingByCounters($feedback->seller);
        });
    }

    public function decreaseRating(Feedback $feedback): void
    {
        DB::transaction(function () use ($feedback) {
            $this->changeProductCounter($feedback->product, false, $feedback->is_positive);
            $this->recalculateProductRatingByCounters($feedback->product);
            $this->changeSellerCounter($feedback->seller, false, $feedback->is_positive);
            $this->recalculateSellerRatingByCounters($feedback->seller);
        });
    }

    public function changeRating(Feedback $feedback, bool $positiveToNegative = true): void
    {
        if ($positiveToNegative) {
            $this->changeProductCounter($feedback->product, false, true);
            $this->changeProductCounter($feedback->product, true, false);
            $this->changeSellerCounter($feedback->seller, false, true);
            $this->changeSellerCounter($feedback->seller, true, false);
        } else {
            $this->changeProductCounter($feedback->product, true, true);
            $this->changeProductCounter($feedback->product, false, false);
            $this->changeSellerCounter($feedback->seller, true, true);
            $this->changeSellerCounter($feedback->seller, false, false);
            $this->recalculateProductRatingByCounters($feedback->product);
        }

        $this->recalculateProductRatingByCounters($feedback->product);
        $this->recalculateSellerRatingByCounters($feedback->seller);
    }

    protected function changeProductCounter(Product $product, bool $increment, bool $positive): void
    {
        $column = $positive ? 'positive_feedbacks_count' : 'negative_feedbacks_count';

        if ($increment) {
            $product->increment($column);
        } else {
            $product->decrement($column);
        }
    }

    public function recalculateProductRatingByCounters(Product $product): void
    {
        $product->rating = $this->calculateRating($product->positive_feedbacks_count, $product->negative_feedbacks_count);
        $product->save();
    }

    protected function changeSellerCounter(User $seller, bool $increment, bool $positive): void
    {
        $column = $positive ? 'positive_feedbacks_count' : 'negative_feedbacks_count';

        if ($increment) {
            $seller->increment($column);
        } else {
            $seller->decrement($column);
        }
    }

    public function recalculateSellerRatingByCounters(User $seller): void
    {
        $seller->seller_rating = $this->calculateRating($seller->positive_feedbacks_count, $seller->negative_feedbacks_count);
        $seller->save();
    }

    public function calculateRating(int $positiveCount, int $negativeCount): float
    {
        return RatingCalculator::calculate($positiveCount, $negativeCount);
    }
}