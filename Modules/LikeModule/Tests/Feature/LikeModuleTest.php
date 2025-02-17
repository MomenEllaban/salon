<?php

namespace Modules\LikeModule\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test LikeModule.
     *
     * @return void
     */
    public function test_backend_likemodules_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/likemodules');

        $response->assertStatus(200);
    }
}
