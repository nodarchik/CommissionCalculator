<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\UserAccount;

class AccountRepository
{
    private array $userAccounts = [];

    public function addUserAccount(UserAccount $userAccount): void
    {
        $this->userAccounts[$userAccount->getUserId()] = $userAccount;
    }

    public function findUserAccountByUserId(int $userId): ?UserAccount
    {
        return $this->userAccounts[$userId] ?? null;
    }

    public function findAllUserAccounts(): array
    {
        return $this->userAccounts;
    }

    public function updateUserAccount(UserAccount $updatedUserAccount): void
    {
        $this->userAccounts[$updatedUserAccount->getUserId()] = $updatedUserAccount;
    }

    public function deleteUserAccount(int $userId): void
    {
        unset($this->userAccounts[$userId]);
    }
}

