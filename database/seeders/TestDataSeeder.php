<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\Hotel;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tour::factory()->create([
            'name' => 'Paris City Tour',
            'description' => 'Amazing tour around Paris',
            'price' => 199.99,
            'start_date' => '2024-07-01',
            'end_date' => '2024-07-05'
        ]);

        Hotel::factory()->create([
            'name' => 'Grand Hotel Paris',
            'description' => 'Luxury hotel in Paris center',
            'address' => '1 Rue de Rivoli, Paris',
            'rating' => 5,
            'price_per_night' => 299.99
        ]);
    }
}
