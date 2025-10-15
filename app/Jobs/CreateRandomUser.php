<?php

namespace App\Jobs;

use App\Services\Demo\DemoUserCreator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateRandomUser implements ShouldQueue
{
    use Queueable;

    public function handle(DemoUserCreator $creator): void
    {
        $creator->createRandomUser();
    }
}
