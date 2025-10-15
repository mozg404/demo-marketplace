<?php

namespace App\Support;

class RatingCalculator
{
    public static function calculate(int $positiveCounter, int $negativeCounter): float
    {
        $total = $positiveCounter + $negativeCounter;

        if ($total > 0) {
            $rating = round(($positiveCounter / $total) * 100, 2);
        } else {
            $rating = 0;
        }

        return $rating;
    }
}