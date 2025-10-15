<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCode200(): void
    {
        // Создаем нужные категории

        $response = $this->get('/');
        $response->assertStatus(200);
    }
}