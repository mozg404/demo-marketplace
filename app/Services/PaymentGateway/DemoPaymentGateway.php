<?php

namespace App\Services\PaymentGateway;

use App\Contracts\PaymentGateway;
use App\Contracts\Sourceable;
use App\Enum\PaymentSource;
use App\Enum\PaymentStatus;
use App\Exceptions\Payment\EmptyExternalIdException;
use App\Exceptions\Payment\UnknownPaymentException;
use App\Exceptions\Payment\UnknownPaymentStatusException;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

class DemoPaymentGateway implements PaymentGateway
{
    public function createPayment(User $user, int $amount, PaymentSource $source, Sourceable $sourceable = null): Payment
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => PaymentStatus::NEW,
            'source' => $source->value,
            'sourceable_type' => $sourceable?->getSourceableType(),
            'sourceable_id' => $sourceable?->getSourceableId(),
        ]);
        $payment->external_id = $this->generateExternalId($payment->id, $amount);
        $payment->save();

        return $payment;
    }

    /**
     * @throws UnknownPaymentException|UnknownPaymentStatusException
     */
    public function validateCallback(array $data): Payment
    {
        $payment = Payment::findByExternalId($data['external_id']);

        if (!isset($payment)) {
            throw new UnknownPaymentException();
        }

        if ($data['status'] === 'failed') {
            return $payment->markAsFailed();
        }

        if ($data['status'] === 'cancelled') {
            return $payment->markAsCancelled();
        }

        if ($data['status'] === 'success') {
            return $payment->markAsSuccess();
        }

        throw new UnknownPaymentStatusException();
    }

    /**
     * @throws EmptyExternalIdException
     */
    public function getPaymentUrl(Payment $payment): string
    {
        if (empty($payment->external_id)) {
            throw new EmptyExternalIdException();
        }

        return route('sandbox', $payment->external_id);
    }

    private function generateExternalId(int $id, int $amount): string
    {
        return base64_encode(json_encode([
            'id' => $id,
            'amount' => $amount,
        ], JSON_THROW_ON_ERROR));
    }
}