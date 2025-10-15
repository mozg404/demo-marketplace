<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testFormStatusCode200(): void
    {
        $this->get('/search')->assertStatus(200);
    }

    public function testStoreStatusCode200(): void
    {
        $this->post('/search', ['search' => 'test'])->assertStatus(200);
    }
}
