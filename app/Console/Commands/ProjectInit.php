<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectInit extends Command
{
    protected $signature = 'project:init';

    protected $description = 'Initialize project';

    public function handle(): void
    {
        $this->call('make:user', [
            'name' => 'Иван',
            'email' => 'user@gmail.com',
            'password' => '123456',
        ]);
    }
}
