<?php

namespace App\Models;

use App\Mail\BookingConfirmation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'hotel_id',
        'customer_name',
        'customer_email',
        'number_of_people',
        'booking_date',
        'status'
    ];

    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELED = 'canceled';

    protected static function booted()
    {
        static::created(function ($booking) {
            Mail::to($booking->customer_email)
                ->send(new BookingConfirmation($booking));
        });
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
