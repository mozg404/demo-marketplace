<?php

namespace App\Jobs;

use App\Services\Demo\DemoOrderCreator;
use App\Services\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CreateRandomOrder implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(UserRepository $repository, DemoOrderCreator $creator): void
    {
        $creator->createAndComplete($repository->getRandomUser());
    }
}
