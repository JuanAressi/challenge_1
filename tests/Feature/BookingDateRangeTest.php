<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Tests\TestCase;

class BookingDateRangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_filter_bookings_by_date_range()
    {
        Booking::factory()->create(['booking_date' => '2024-01-01']);
        Booking::factory()->create(['booking_date' => '2024-02-15']);
        Booking::factory()->create(['booking_date' => '2024-03-30']);

        $response = $this->getJson('/api/bookings?start_date=2024-02-01&end_date=2024-02-28');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.booking_date', '2024-02-15');
    }

    /** @test */
    public function it_can_filter_bookings_with_only_start_date()
    {
        Booking::factory()->create(['booking_date' => '2024-01-01']);
        Booking::factory()->create(['booking_date' => '2024-02-15']);

        $response = $this->getJson('/api/bookings?start_date=2024-02-01');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.booking_date', '2024-02-15');
    }

    /** @test */
    public function it_can_filter_bookings_with_only_end_date()
    {
        Booking::factory()->create(['booking_date' => '2024-01-01']);
        Booking::factory()->create(['booking_date' => '2024-02-15']);

        $response = $this->getJson('/api/bookings?end_date=2024-01-31');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.booking_date', '2024-01-01');
    }

    /** @test */
    public function it_returns_empty_array_when_no_bookings_in_date_range()
    {
        Booking::factory()->create(['booking_date' => '2024-01-01']);

        $response = $this->getJson('/api/bookings?start_date=2024-02-01&end_date=2024-03-01');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function date_range_search_performs_within_acceptable_time()
    {
        Booking::factory()->count(1000)->create();

        $benchmark = Benchmark::measure([
            'search' => fn () => $this->getJson('/api/bookings?start_date=2024-01-01&end_date=2024-12-31')
        ]);

        $this->assertTrue($benchmark['search'] < 300);
    }
} 