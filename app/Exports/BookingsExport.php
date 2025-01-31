<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Booking::with(['tour', 'hotel'])->get();
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Customer Email',
            'Tour Name',
            'Hotel Name',
            'Number of People',
            'Booking Date'
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->customer_name,
            $booking->customer_email,
            $booking->tour->name,
            $booking->hotel->name,
            $booking->number_of_people,
            $booking->booking_date
        ];
    }
} 