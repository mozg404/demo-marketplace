<?php

namespace App\Services;

use Illuminate\Contracts\Session\Session;

class Toaster
{
    private const SESSION_KEY = 'toasts';

    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    private function add(string $message, string|null $description = null, string $type = 'default'): void
    {
        $this->session->push(self::SESSION_KEY, [
            'type' => $type,
            'message' => $message,
            'description' => $description,
        ]);
    }

    public function toast(string $message, string $description = null): void
    {
        $this->add($message, $description);
    }

    public function info(string $message, string $description = null): void
    {
        $this->add($message, $description, 'info');
    }

    public function success(string $message, string $description = null): void
    {
        $this->add($message, $description, 'success');
    }

    public function error(string $message, string $description = null): void
    {
        $this->add($message, $description, 'error');
    }

    /**
     * Возвращает все элементы
     * @return array
     */
    public function all(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    /**
     * Возвращает все элементы и очищает сессию
     * @return array
     */
    public function pull(): array
    {
        return $this->session->pull(self::SESSION_KEY, []);
    }
}