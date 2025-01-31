<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('customer_name')) {
            $query->where('customer_name', 'like', '%' . $request->customer_name . '%');
        }

        if ($request->has('tour_name')) {
            $query->whereHas('tour', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->tour_name . '%');
            });
        }

        if ($request->has('hotel_name')) {
            $query->whereHas('hotel', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->hotel_name . '%');
            });
        }

        if ($request->has('start_date')) {
            $query->where('booking_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('booking_date', '<=', $request->end_date);
        }

        $sortField = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        $allowedSortFields = ['customer_name', 'booking_date', 'number_of_people', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        $perPage = $request->input('per_page', 15);

        return $query->with(['tour', 'hotel'])->paginate($perPage);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = Booking::create($request->validated());

        Mail::to($booking->customer_email)
            ->send(new BookingConfirmation($booking));

        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        return response()->json($booking, 200);
    }

    public function update(UpdateBookingRequest $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update($request->validated());
        
        return response()->json($booking);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(null, 204);
    }

    public function export()
    {
        return Excel::download(new BookingsExport, 'bookings.csv');
    }

    public function cancel($id)
    {
        $booking = Booking::find($id);
        
        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }
        
        if ($booking->status === Booking::STATUS_CANCELED) {
            return response()->json([
                'message' => 'Booking is already canceled',
                'status' => $booking->status
            ], 422);
        }
        
        $booking->update(['status' => Booking::STATUS_CANCELED]);
        
        return response()->json([
            'message' => 'Booking canceled successfully',
            'status' => $booking->status
        ], 200);
    }
}
