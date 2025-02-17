<?php

namespace Modules\Booking\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test Booking.
     *
     * @return void
     */
    public function test_backend_bookings_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/bookings');

        $response->assertStatus(200);
    }
}
