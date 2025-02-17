<?php

namespace Modules\Currency\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test Currency.
     *
     * @return void
     */
    public function test_backend_currencies_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/currencies');

        $response->assertStatus(200);
    }
}
