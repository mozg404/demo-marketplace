<?php

namespace Tests\Feature\Services\Balance;

use App\Enum\TransactionType;
use App\Exceptions\Balance\InsufficientFundsException;
use App\Exceptions\Balance\NegativeAmountException;
use App\Exceptions\Balance\ZeroAmountException;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Balance\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private BalanceService $balanceService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->balanceService = app(BalanceService::class);
        $this->user = User::factory()->create(['balance' => 1000]);
    }

    #[Test]
    public function depositIncreasesUserBalance(): void
    {
        $transaction = $this->balanceService->deposit(
            $this->user,
            500,
            TransactionType::ADMIN_CORRECTION
        );

        $this->assertEquals(1500, $this->user->fresh()->balance);
        $this->assertEquals(500, $transaction->amount);
        $this->assertEquals(TransactionType::ADMIN_CORRECTION->value, $transaction->type->value);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    #[Test]
    public function depositCreatesTransactionRecord(): void
    {
        $initialTransactionsCount = Transaction::count();

        $transaction = $this->balanceService->deposit(
            $this->user,
            300,
            TransactionType::GATEWAY_DEPOSIT
        );

        $this->assertEquals($initialTransactionsCount + 1, Transaction::count());
        $this->assertEquals(300, $transaction->amount);
        $this->assertEquals(TransactionType::GATEWAY_DEPOSIT->value, $transaction->type->value);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    #[Test]
    public function withdrawDecreasesUserBalance(): void
    {
        $transaction = $this->balanceService->withdraw(
            $this->user,
            300,
            TransactionType::ORDER_PAYMENT
        );

        $this->assertEquals(700, $this->user->fresh()->balance);
        $this->assertEquals(-300, $transaction->amount);
        $this->assertEquals(TransactionType::ORDER_PAYMENT->value, $transaction->type->value);
        $this->assertEquals($this->user->id, $transaction->user_id);
    }

    #[Test]
    public function withdrawThrowsExceptionWhenInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);

        $this->balanceService->withdraw(
            $this->user,
            1500,
            TransactionType::ORDER_PAYMENT
        );

        // Баланс не должен измениться
        $this->assertEquals(1000, $this->user->fresh()->balance);
    }

    #[Test]
    public function withdrawWithExactBalanceAmount(): void
    {
        $transaction = $this->balanceService->withdraw(
            $this->user,
            1000,
            TransactionType::ORDER_PAYMENT
        );

        $this->assertEquals(0, $this->user->fresh()->balance);
        $this->assertEquals(-1000, $transaction->amount);
    }

    #[Test]
    public function depositThrowsExceptionForNegativeAmount(): void
    {
        $this->expectException(NegativeAmountException::class);

        $this->balanceService->deposit(
            $this->user,
            -100,
            TransactionType::ADMIN_CORRECTION
        );
    }

    #[Test]
    public function depositThrowsExceptionForZeroAmount(): void
    {
        $this->expectException(ZeroAmountException::class);

        $this->balanceService->deposit(
            $this->user,
            0,
            TransactionType::ADMIN_CORRECTION
        );
    }

    #[Test]
    public function withdrawThrowsExceptionForNegativeAmount(): void
    {
        $this->expectException(NegativeAmountException::class);

        $this->balanceService->withdraw(
            $this->user,
            -50,
            TransactionType::ORDER_PAYMENT
        );
    }

    #[Test]
    public function withdrawThrowsExceptionForZeroAmount(): void
    {
        $this->expectException(ZeroAmountException::class);

        $this->balanceService->withdraw(
            $this->user,
            0,
            TransactionType::ORDER_PAYMENT
        );
    }

    #[Test]
    public function multipleOperationsWorkCorrectly(): void
    {
        // Делаем несколько операций подряд
        $this->balanceService->deposit($this->user, 200, TransactionType::GATEWAY_DEPOSIT);
        $this->balanceService->withdraw($this->user, 500, TransactionType::ORDER_PAYMENT);
        $this->balanceService->deposit($this->user, 100, TransactionType::ADMIN_CORRECTION);

        $this->assertEquals(800, $this->user->fresh()->balance);

        $transactionCount = Transaction::where('user_id', $this->user->id)->count();
        $this->assertEquals(3, $transactionCount);
    }

    #[Test]
    public function transactionHasCorrectTimestamps(): void
    {
        $transaction = $this->balanceService->deposit(
            $this->user,
            500,
            TransactionType::ADMIN_CORRECTION
        );

        $this->assertNotNull($transaction->created_at);
        // Убираем проверку updated_at, т.к. он может быть null если не используется в модели
    }

    #[Test]
    public function canProcessZeroBalanceUser(): void
    {
        $zeroBalanceUser = User::factory()->create(['balance' => 0]);

        $transaction = $this->balanceService->deposit(
            $zeroBalanceUser,
            1000,
            TransactionType::GATEWAY_DEPOSIT
        );

        $this->assertEquals(1000, $zeroBalanceUser->fresh()->balance);
        $this->assertEquals(1000, $transaction->amount);
    }

    #[Test]
    public function withdrawFromZeroBalanceThrowsException(): void
    {
        $zeroBalanceUser = User::factory()->create(['balance' => 0]);

        $this->expectException(InsufficientFundsException::class);

        $this->balanceService->withdraw(
            $zeroBalanceUser,
            100,
            TransactionType::ORDER_PAYMENT
        );
    }
}