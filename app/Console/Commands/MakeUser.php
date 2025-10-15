<?php

namespace App\Console\Commands;

use App\Services\User\UserRegistrar;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Console\Command;

class MakeUser extends Command
{
    protected $signature = 'make:user {name} {email} {password}';

    protected $description = 'Register new user';

    public function handle(UserRegistrar $registrar): void
    {
        try {
            $registrar->register(
                name: $this->argument('name'),
                email: new Email($this->argument('email')),
                password: new Password($this->argument('password')),
            );
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
