<?php

namespace Tests\Feature\Services\User;

use App\Exceptions\User\EmailAlreadyExistsException;
use App\Models\User;
use App\Services\Auth\EmailVerificator;
use App\Services\Auth\PasswordHasher;
use App\Services\User\UserChecker;
use App\Services\User\UserCreator;
use App\ValueObjects\Email;
use App\ValueObjects\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserCreatorTest extends TestCase
{
    use RefreshDatabase;

    private UserCreator $userCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userCreator = $this->app->make(UserCreator::class);
    }

    public function testSuccessRegister(): void
    {
        $userCheckerMock = $this->createMock(UserChecker::class);
        $passwordHasherMock = $this->createMock(PasswordHasher::class);
        $emailVerificatorMock = $this->createMock(EmailVerificator::class);

        $name = 'Petr';
        $plainEmail = 'test@mail.ru';
        $email = new Email($plainEmail);
        $plainPassword = '12345678';
        $password = new Password($plainPassword);

        $userCheckerMock->expects($this->once())->method('ensureUniqueEmail')->with($plainEmail);
        $passwordHasherMock->expects($this->once())->method('hash')->with($plainPassword);
        $emailVerificatorMock->expects($this->once())->method('verifyWithoutToken');
        $userCreator = new UserCreator($userCheckerMock, $passwordHasherMock, $emailVerificatorMock);

        $user = $userCreator->create($name, $email, $password);

        $this->assertEquals($name, $user->name);
        $this->assertEquals($email->value, $user->email);
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $name,
            'email' => $email->value,
        ]);
    }

    public function testDuplicate(): void
    {
        $email = 'duplicate@mail.ru';
        User::factory()->create(['email' => $email]);

        $this->expectException(EmailAlreadyExistsException::class);
        $this->userCreator->create('Name', new Email($email), new Password('12345678'));
    }

    public function testEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->userCreator->create('', new Email('test@mail.ru'), new Password('12345678'));
    }

    public function testNameShorterThen3Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->userCreator->create('xx', new Email('test@mail.ru'), new Password('12345678'));
    }

    public function testNameLongerThan255Characters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->userCreator->create(Str::random(256), new Email('test@mail.ru'), new Password('12345678'));
    }
}
