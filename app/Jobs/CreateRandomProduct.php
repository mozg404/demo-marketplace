<?php

namespace App\Jobs;

use App\Services\Demo\DemoProductCreator;
use App\Services\Demo\DemoProductList;
use App\Services\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CreateRandomProduct implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(
        UserRepository $repository,
        DemoProductList $productList,
        DemoProductCreator $productCreator
    ): void {
        $productCreator->create($repository->getRandomUser(), $productList->random());
    }
}
