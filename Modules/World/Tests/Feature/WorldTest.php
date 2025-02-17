<?php

namespace Modules\World\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorldTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test World.
     *
     * @return void
     */
    public function test_backend_worlds_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/worlds');

        $response->assertStatus(200);
    }
}
