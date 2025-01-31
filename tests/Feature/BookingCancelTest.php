<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingCancelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_cancel_a_booking()
    {
        $booking = Booking::factory()->create(['status' => 'confirmed']);

        $response = $this->patchJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Booking canceled successfully',
                'status' => 'canceled'
            ]);

        $this->assertEquals('canceled', $booking->fresh()->status);
    }

    /** @test */
    public function it_cannot_cancel_an_already_canceled_booking()
    {
        $booking = Booking::factory()->create(['status' => 'canceled']);

        $response = $this->patchJson("/api/bookings/{$booking->id}/cancel");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Booking is already canceled',
                'status' => 'canceled'
            ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_booking()
    {
        $response = $this->patchJson("/api/bookings/999/cancel");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Booking not found'
            ]);
    }
} 