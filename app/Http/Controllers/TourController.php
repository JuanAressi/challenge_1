<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTourRequest;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::query();

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 15);
        return $query->paginate($perPage);
    }

    public function store(StoreTourRequest $request)
    {
        $tour = Tour::create($request->validated());
        return response()->json($tour, 201);
    }

    public function show(Tour $tour)
    {
        return response()->json($tour, 200);
    }

    public function update(StoreTourRequest $request, $id)
    {
        $tour = Tour::findOrFail($id);
        $tour->update($request->validated());
        return response()->json($tour);
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return response()->json(null, 204);
    }
}
