<?php

namespace Tests\Feature\Services\User;

use App\Services\User\UserCreator;
use App\Services\User\UserRegistrar;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserRegistrarTest extends TestCase
{
    use RefreshDatabase;

    public function testCorrectRegister()
    {
        Event::fake();

        $userCreatorMock = $this->createMock(UserCreator::class);
        $name = 'Petr';
        $plainEmail = 'test@mail.ru';
        $email = new Email($plainEmail);
        $plainPassword = '12345678';
        $password = new Password($plainPassword);

        $userCreatorMock->expects($this->once())->method('create')->with($name, $email, $password);
        $userRegistrar = new UserRegistrar($userCreatorMock);

        $userRegistrar->register($name, $email, $password);
    }
}
