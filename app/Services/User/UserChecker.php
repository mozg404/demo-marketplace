<?php

namespace App\Services\User;

use App\Exceptions\User\EmailAlreadyExistsException;

readonly class UserChecker
{
    public function __construct(
        private UserQuery $userQuery,
    ) {
    }

    public function ensureUniqueEmail(string $email): void
    {
        if ($this->checkExistsByEmail($email)) {
            throw new EmailAlreadyExistsException();
        }
    }

    public function checkExistsByEmail(string $email): bool
    {
        return $this->userQuery->query()->checkExistsByEmail($email);
    }
}