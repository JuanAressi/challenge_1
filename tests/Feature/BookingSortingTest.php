<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Tests\TestCase;

class BookingSortingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_sort_bookings_by_customer_name()
    {
        Booking::factory()->create(['customer_name' => 'Juan']);
        Booking::factory()->create(['customer_name' => 'Augusto']);
        
        $response = $this->getJson('/api/bookings?sort_by=customer_name&sort_direction=asc');
        
        $response->assertStatus(200);
        $bookings = $response->json('data');
        $this->assertEquals('Augusto', $bookings[0]['customer_name']);
        $this->assertEquals('Juan', $bookings[1]['customer_name']);
    }

    /** @test */
    public function it_can_sort_bookings_by_customer_name_descending()
    {
        Booking::factory()->create(['customer_name' => 'Augusto']);
        Booking::factory()->create(['customer_name' => 'Juan']);
        
        $response = $this->getJson('/api/bookings?sort_by=customer_name&sort_direction=desc');
        
        $response->assertStatus(200);
        $bookings = $response->json('data');
        $this->assertEquals('Juan', $bookings[0]['customer_name']);
        $this->assertEquals('Augusto', $bookings[1]['customer_name']);
    }

    /** @test */
    public function it_can_sort_bookings_by_date()
    {
        Booking::factory()->create(['booking_date' => '2024-01-01']);
        Booking::factory()->create(['booking_date' => '2024-12-31']);
        
        $response = $this->getJson('/api/bookings?sort_by=booking_date&sort_direction=desc');
        
        $response->assertStatus(200);
        $bookings = $response->json('data');
        $this->assertEquals('2024-12-31', $bookings[0]['booking_date']);
        $this->assertEquals('2024-01-01', $bookings[1]['booking_date']);
    }

    /** @test */
    public function it_uses_default_sorting_when_invalid_field_provided()
    {
        Booking::factory()->count(2)->create();
        
        $response = $this->getJson('/api/bookings?sort_by=invalid_field');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function sorting_performs_within_acceptable_time()
    {
        Booking::factory()->count(1000)->create();

        $benchmark = Benchmark::measure([
            'sort' => fn () => $this->getJson('/api/bookings?sort_by=customer_name&sort_direction=asc')
        ]);

        $this->assertTrue($benchmark['sort'] < 300);
    }
} 