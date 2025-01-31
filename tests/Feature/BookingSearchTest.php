<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Tests\TestCase;

class BookingSearchTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        $tour = Tour::factory()->create(['name' => 'Paris Tour']);
        $hotel = Hotel::factory()->create(['name' => 'Grand Hotel']);
        Booking::factory()->create([
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'customer_name' => 'John Doe'
        ]);
    }

    /** @test */
    public function it_can_search_by_customer_name()
    {
        $response = $this->getJson('/api/bookings?customer_name=John');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.customer_name', 'John Doe');
    }

    /** @test */
    public function it_can_search_by_tour_name()
    {
        $response = $this->getJson('/api/bookings?tour_name=Paris');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.tour.name', 'Paris Tour');
    }

    /** @test */
    public function it_can_search_by_hotel_name()
    {
        $response = $this->getJson('/api/bookings?hotel_name=Grand');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.hotel.name', 'Grand Hotel');
    }

    /** @test */
    public function it_returns_empty_array_when_no_matches()
    {
        $response = $this->getJson('/api/bookings?customer_name=NonExistent');
        
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    /** @test */
    public function it_can_handle_multiple_search_criteria()
    {
        $response = $this->getJson('/api/bookings?customer_name=John&tour_name=Paris');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function search_performs_within_acceptable_time()
    {
        Booking::factory()->count(1000)->create();

        $benchmark = Benchmark::measure([
            'search' => fn () => $this->getJson('/api/bookings?customer_name=John')
        ]);

        $this->assertTrue($benchmark['search'] < 300);
    }

} 