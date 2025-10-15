<?php

namespace Tests\Feature\Services\Payment;

use App\Exceptions\Payment\EmptyExternalIdException;
use App\Exceptions\Payment\UnknownPaymentException;
use App\Exceptions\Payment\UnknownPaymentStatusException;
use App\Models\Payment;
use App\Services\PaymentGateway\DemoPaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoPaymentGatewayTest extends TestCase
{
    use RefreshDatabase;
    
    private DemoPaymentGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = $this->app->make(DemoPaymentGateway::class);
    }

    public function testUnknownPayment(): void
    {
        $this->expectException(UnknownPaymentException::class);
        $this->gateway->validateCallback(['external_id' => 'non-existent-id']);
    }

    public function testUnknownPaymentStatusAtValidationCallback()
    {
        $payment = Payment::factory()->create(['external_id' => 'test']);
        $this->expectException(UnknownPaymentStatusException::class);
        $this->gateway->validateCallback(['external_id' => 'test', 'status' => 'unknown']);
    }

    public function testChangeStatusAtValidationCallbackToSuccess()
    {
        Payment::factory()->create(['external_id' => 'test']);
        $payment = $this->gateway->validateCallback(['external_id' => 'test', 'status' => 'success']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
        ]);
    }

    public function testChangeStatusAtValidationCallbackToCancelled()
    {
        Payment::factory()->create(['external_id' => 'test']);
        $payment = $this->gateway->validateCallback(['external_id' => 'test', 'status' => 'cancelled']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'cancelled',
        ]);
    }

    public function testChangeStatusAtValidationCallbackToFailed()
    {
        Payment::factory()->create(['external_id' => 'test']);
        $payment = $this->gateway->validateCallback(['external_id' => 'test', 'status' => 'failed']);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'failed',
        ]);
    }

    public function testEmptyExternalIdForGettingPaymentUrl()
    {
        $payment = Payment::factory()->make(['external_id' => '']);
        $this->expectException(EmptyExternalIdException::class);
        $this->gateway->getPaymentUrl($payment);
    }

    public function testNullableExternalIdForGettingPaymentUrl()
    {
        $payment = Payment::factory()->make(['external_id' => null]);
        $this->expectException(EmptyExternalIdException::class);
        $this->gateway->getPaymentUrl($payment);
    }
}
