<?php

namespace Tests\Feature\Services\User;

use App\Exceptions\User\EmailAlreadyExistsException;
use App\Models\User;
use App\Services\User\UserChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCheckerTest extends TestCase
{
    use RefreshDatabase;

    private UserChecker $userChecker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userChecker = $this->app->make(UserChecker::class);
    }

    public function testExistsByEmail(): void
    {
        $email = 'exists@gmail.com';
        $email2 = 'non-exists@gmail.com';

        User::factory()->create([
            'email' => $email,
        ]);

        $this->assertTrue($this->userChecker->checkExistsByEmail($email));
        $this->assertFalse($this->userChecker->checkExistsByEmail($email2));
    }

    public function testEnsureUniqueEmail(): void
    {
        $email = 'exists@gmail.com';
        User::factory()->create(['email' => $email]);

        $this->expectException(EmailAlreadyExistsException::class);
        $this->userChecker->ensureUniqueEmail($email);
    }
}
