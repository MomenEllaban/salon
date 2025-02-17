<?php

namespace Modules\Package\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test Package.
     *
     * @return void
     */
    public function test_backend_packages_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/packages');

        $response->assertStatus(200);
    }
}
