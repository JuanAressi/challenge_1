<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tour_id' => 'required|exists:tours,id',
            'hotel_id' => 'required|exists:hotels,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'number_of_people' => 'required|integer|min:1',
            'booking_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'tour_id.required' => 'A tour must be selected',
            'tour_id.exists' => 'The selected tour is invalid',
            'hotel_id.required' => 'A hotel must be selected',
            'hotel_id.exists' => 'The selected hotel is invalid',
            'customer_name.required' => 'The customer name is required',
            'customer_email.required' => 'The customer email is required',
            'customer_email.email' => 'Please provide a valid email address',
            'number_of_people.required' => 'The number of people is required',
            'number_of_people.min' => 'At least one person is required',
            'booking_date.required' => 'The booking date is required',
            'booking_date.date' => 'Please provide a valid date',
        ];
    }
} 