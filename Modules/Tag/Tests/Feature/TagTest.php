<?php

namespace Modules\Tag\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test Tag.
     *
     * @return void
     */
    public function test_backend_tags_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/tags');

        $response->assertStatus(200);
    }
}
