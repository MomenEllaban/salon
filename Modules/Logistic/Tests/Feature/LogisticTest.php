<?php

namespace Modules\Logistic\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogisticTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test Logistic.
     *
     * @return void
     */
    public function test_backend_logistics_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/logistics');

        $response->assertStatus(200);
    }
}
