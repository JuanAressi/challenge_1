<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Tour;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Tests\TestCase;
use Maatwebsite\Excel\Facades\Excel;

class BookingExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_export_bookings_to_csv()
    {
        Excel::fake();

        $tour = Tour::factory()->create(['name' => 'Paris Tour']);
        $hotel = Hotel::factory()->create(['name' => 'Grand Hotel']);
        Booking::factory()->create([
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'customer_name' => 'John Doe'
        ]);

        $response = $this->get('/api/bookings/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('bookings.csv', function($export) {
            return $export->collection()->count() === 1;
        });
    }
} 