<?php

namespace App\Http\Controllers;

use JsonException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SandboxController extends Controller
{
    /**
     * @throws JsonException
     */
    public function index(string $hash): Response
    {
        $decodedHash = base64_decode($hash);
        $data = json_decode($decodedHash, true);

        if (!isset($data['amount'])) {
            abort(404);
        }

        return Inertia::render('demo/SandboxIndexPage', [
            'amount' => $data['amount'],
            'hash' => $hash,
        ]);
    }

    public function success(string $hash): RedirectResponse
    {
        return redirect()->route('payment.callback', [
            'external_id' => $hash,
            'status' => 'success'
        ]);
    }

    public function failed(string $hash): RedirectResponse
    {
        return redirect()->route('payment.callback', [
            'external_id' => $hash,
            'status' => 'failed'
        ]);
    }

    public function cancelled(string $hash): RedirectResponse
    {
        return redirect()->route('payment.callback', [
            'external_id' => $hash,
            'status' => 'cancelled'
        ]);
    }
}
