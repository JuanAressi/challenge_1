<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_paginate_bookings()
    {
        Booking::factory()->count(30)->create();

        $response = $this->getJson('/api/bookings?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure([
                'data',
                'total',
                'per_page'
            ]);
    }

    /** @test */
    public function it_can_paginate_tours()
    {
        Tour::factory()->count(30)->create();

        $response = $this->getJson('/api/tours?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_can_paginate_hotels()
    {
        Hotel::factory()->count(30)->create();

        $response = $this->getJson('/api/hotels?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function pagination_performs_within_acceptable_time()
    {
        Booking::factory()->count(1000)->create();

        $benchmark = Benchmark::measure([
            'pagination' => fn () => $this->getJson('/api/bookings?per_page=50')
        ]);

        $this->assertTrue($benchmark['pagination'] < 300);
    }
} 