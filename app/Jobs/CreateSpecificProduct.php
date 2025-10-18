<?php

namespace App\Jobs;

use App\Data\Demo\DemoProductData;
use App\Services\Demo\DemoProductCreator;
use App\Services\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class CreateSpecificProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public DemoProductData $data
    ) {
    }

    public function handle(UserRepository $repository, DemoProductCreator $productCreator): void
    {
        $productCreator->create($repository->getRandomUser(), $this->data);
    }
}
