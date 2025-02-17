<?php

namespace Modules\Constant\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test Constant.
     *
     * @return void
     */
    public function test_backend_constants_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/constants');

        $response->assertSee('Constant');

        $response->assertStatus(200);
    }

    public function test_backend_constants_filter_list_data()
    {
        $this->signInAsAdmin();

        $response = $this->get('/app/constants/index_data');

        $response->assertStatus(200);
    }

    // public function test_backend_constants_list_data()
    // {
    //     $response = $this->get('/app/constants');

    //     $response->assertStatus(200);
    // }

    // public function test_backend_constants_edit_data()
    // {
    //     $response = $this->get('/app/constants');

    //     $response->assertStatus(200);
    // }

    // public function test_backend_constants_store_data()
    // {
    //     $response = $this->get('/app/constants');

    //     $response->assertStatus(200);
    // }

    // public function test_backend_constants_update_data()
    // {
    //     $response = $this->get('/app/constants');

    //     $response->assertStatus(200);
    // }

    // public function test_backend_constants_delete_data()
    // {
    //     $response = $this->get('/app/constants');

    //     $response->assertStatus(200);
    // }
}
