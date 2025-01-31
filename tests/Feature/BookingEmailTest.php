<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Tour;
use App\Mail\BookingConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_sent_when_booking_is_created()
    {
        Mail::fake();

        $tour = Tour::factory()->create();
        $hotel = Hotel::factory()->create();
        $bookingData = [
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'number_of_people' => 2,
            'booking_date' => '2024-07-01',
        ];

        $response = $this->postJson('/api/bookings', $bookingData);

        $response->assertStatus(201);
        Mail::assertSent(BookingConfirmation::class, function ($mail) use ($bookingData) {
            return $mail->hasTo($bookingData['customer_email']);
        });
    }
}
