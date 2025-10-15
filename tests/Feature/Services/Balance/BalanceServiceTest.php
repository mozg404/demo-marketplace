<?php

namespace Tests\Feature\Services\Balance;

use App\Enum\TransactionType;
use App\Exceptions\Balance\NegativeAmountException;
use App\Exceptions\Balance\ZeroAmountException;
use App\Models\User;
use App\Services\Balance\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private BalanceService $balanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->balanceService = $this->app->make(BalanceService::class);
    }

    public function testNegativeAmountWhenDeposit(): void
    {
        $user = User::factory()->make();
        $this->expectException(NegativeAmountException::class);
        $this->balanceService->deposit($user, -100,TransactionType::GATEWAY_DEPOSIT);
    }

    public function testZeroAmountWhenDeposit(): void
    {
        $user = User::factory()->make();
        $this->expectException(ZeroAmountException::class);
        $this->balanceService->deposit($user, 0,TransactionType::GATEWAY_DEPOSIT);
    }

    public function testCompleteDeposit()
    {
        $balance = 100;
        $amount = 200;
        $type = TransactionType::GATEWAY_DEPOSIT;
        $user = User::factory()->create(['balance' => $balance]);
        $transaction = $this->balanceService->deposit($user, $amount,$type);

        // Правильная сумма в транзакции
        $this->assertEquals($amount, $transaction->amount);
        // Правильный тип
        $this->assertEquals($type, $transaction->type);
        // Наличие записи в БД
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => $type->value,
        ]);
        // Увеличение баланса пользователя
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => $balance + $amount,
        ]);
    }

    public function testNegativeAmountWithdraw(): void
    {
        $user = User::factory()->make();
        $this->expectException(NegativeAmountException::class);
        $this->balanceService->withdraw($user, -100,TransactionType::GATEWAY_DEPOSIT);
    }

    public function testZeroAmountWhenWithdraw(): void
    {
        $user = User::factory()->make();
        $this->expectException(ZeroAmountException::class);
        $this->balanceService->withdraw($user, 0,TransactionType::GATEWAY_DEPOSIT);
    }

    public function testCompleteWithdraw()
    {
        $balance = 800;
        $amount = 500;
        $type = TransactionType::GATEWAY_DEPOSIT;
        $user = User::factory()->create(['balance' => $balance]);
        $transaction = $this->balanceService->withdraw($user, $amount,$type);

        // Правильная сумма в транзакции
        $this->assertEquals(-$amount, $transaction->amount);
        // Правильный тип
        $this->assertEquals($type, $transaction->type);
        // Наличие записи в БД
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'amount' => -$amount,
            'type' => $type->value,
        ]);
        // Уменьшение баланса пользователя
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'balance' => $balance - $amount,
        ]);
    }
}
