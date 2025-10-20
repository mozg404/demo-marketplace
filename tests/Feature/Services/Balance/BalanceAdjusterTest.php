<?php

namespace Tests\Feature\Services\Balance;

use App\Enum\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Balance\BalanceAdjuster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use InvalidArgumentException;

class BalanceAdjusterTest extends TestCase
{
    use RefreshDatabase;

    private BalanceAdjuster $balanceAdjuster;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->balanceAdjuster = app(BalanceAdjuster::class);
        $this->user = User::factory()->create(['balance' => 1000]);
    }

    #[Test]
    public function setBalanceIncreasesBalanceWhenTargetIsHigher(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 1500);

        $this->assertEquals(1500, $this->user->fresh()->balance);
        $this->assertEquals(500, $transaction->amount);
        $this->assertEquals(TransactionType::ADMIN_CORRECTION->value, $transaction->type->value);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    #[Test]
    public function setBalanceDecreasesBalanceWhenTargetIsLower(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 500);

        $this->assertEquals(500, $this->user->fresh()->balance);
        $this->assertEquals(-500, $transaction->amount);
        $this->assertEquals(TransactionType::ADMIN_CORRECTION->value, $transaction->type->value);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    #[Test]
    public function setBalanceThrowsExceptionWhenTargetIsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Баланс не может быть отрицательным');

        $this->balanceAdjuster->setBalance($this->user, -100);
    }

    #[Test]
    public function setBalanceThrowsExceptionWhenTargetIsSame(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Баланс итак целевой');

        $this->balanceAdjuster->setBalance($this->user, 1000);
    }

    #[Test]
    public function setBalanceCreatesDepositTransactionForIncrease(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 1200);

        $this->assertEquals(200, $transaction->amount); // Депозит на разницу
        $this->assertEquals(TransactionType::ADMIN_CORRECTION->value, $transaction->type->value);
    }

    #[Test]
    public function setBalanceCreatesWithdrawTransactionForDecrease(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 800);

        $this->assertEquals(-200, $transaction->amount); // Снятие разницы
        $this->assertEquals(TransactionType::ADMIN_CORRECTION->value, $transaction->type->value);
    }

    #[Test]
    public function setBalanceWorksWithZeroCurrentBalance(): void
    {
        $zeroBalanceUser = User::factory()->create(['balance' => 0]);

        $transaction = $this->balanceAdjuster->setBalance($zeroBalanceUser, 500);

        $this->assertEquals(500, $zeroBalanceUser->fresh()->balance);
        $this->assertEquals(500, $transaction->amount);
    }

    #[Test]
    public function setBalanceWorksToZeroBalance(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 0);

        $this->assertEquals(0, $this->user->fresh()->balance);
        $this->assertEquals(-1000, $transaction->amount);
    }

    #[Test]
    public function setBalanceWorksWithLargeDifference(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 5000);

        $this->assertEquals(5000, $this->user->fresh()->balance);
        $this->assertEquals(4000, $transaction->amount);
    }

    #[Test]
    public function setBalanceMultipleTimesCorrectly(): void
    {
        // Первая корректировка
        $this->balanceAdjuster->setBalance($this->user, 2000);
        $this->assertEquals(2000, $this->user->fresh()->balance);

        // Вторая корректировка
        $this->balanceAdjuster->setBalance($this->user, 1500);
        $this->assertEquals(1500, $this->user->fresh()->balance);

        // Третья корректировка
        $this->balanceAdjuster->setBalance($this->user, 3000);
        $this->assertEquals(3000, $this->user->fresh()->balance);

        // Проверяем что создались все транзакции
        $transactions = Transaction::where('user_id', $this->user->id)->get();
        $this->assertCount(3, $transactions);

        $amounts = $transactions->pluck('amount')->sort()->values();
        $this->assertEquals([-500, 1000, 1500], $amounts->toArray());
    }

    #[Test]
    public function setBalanceTransactionHasCorrectType(): void
    {
        $transaction = $this->balanceAdjuster->setBalance($this->user, 1500);

        $this->assertEquals(TransactionType::ADMIN_CORRECTION->value, $transaction->type->value);
        $this->assertNull($transaction->transactionable_type); // Админские корректировки без привязки
        $this->assertNull($transaction->transactionable_id);
    }
}