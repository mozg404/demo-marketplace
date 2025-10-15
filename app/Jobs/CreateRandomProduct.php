<?php

namespace App\Jobs;

use App\Services\Demo\DemoProductCreator;
use App\Services\Demo\DemoProductList;
use App\Services\User\UserQuery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class CreateRandomProduct implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(
        UserQuery $userQuery,
        DemoProductList $productList,
        DemoProductCreator $productCreator
    ): void {
        $productCreator->create($userQuery->getRandomUser(), $productList->random());
    }
}
