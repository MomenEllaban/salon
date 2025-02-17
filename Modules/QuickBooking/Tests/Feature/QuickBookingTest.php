<?php

namespace Modules\QuickBooking\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickBookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test QuickBooking.
     *
     * @return void
     */
    public function test_backend_quickbookings_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('quick-booking');

        $response->assertStatus(200);
    }
}
