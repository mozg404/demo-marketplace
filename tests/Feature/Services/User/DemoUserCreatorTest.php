<?php

namespace Tests\Feature\Services\User;

use App\Services\Demo\DemoUserCreator;
use App\Services\User\UserAvatarChanger;
use App\Services\User\UserCreator;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DemoUserCreatorTest extends TestCase
{
    public function testCorrectCreation(): void
    {
        Event::fake();

        $userCreatorMock = $this->createMock(UserCreator::class);
        $avatarChangerMock = $this->createMock(UserAvatarChanger::class);

        $name = 'Petr';
        $emailPlain = 'test@mail.ru';
        $email = new Email($emailPlain);
        $passwordPlain = '12345678';
        $password = new Password($passwordPlain);
        $avatarPath = 'testPath.jpg';
        $isAdmin = true;

        $userCreatorMock->expects($this->once())->method('create')->with($name, $email, $password, true, $isAdmin);
        $avatarChangerMock->expects($this->once())->method('changeFromPath');
        $demoUserCreator = new DemoUserCreator($userCreatorMock, $avatarChangerMock);

        $demoUserCreator->create($name, $emailPlain, $passwordPlain, $avatarPath, $isAdmin);
    }
}
