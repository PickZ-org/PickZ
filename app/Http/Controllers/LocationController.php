<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationType;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('location.index', ['locationTypes' => LocationType::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:locations',
            'description' => 'string|nullable',
            'barcode' => 'required|string|unique:locations',
            'location_type_id' => 'required|numeric|exists:location_types,id',
            'location_order' => 'integer|min:0|nullable',
        ]);
        Location::create($validatedData);
        return response()->json([
            'success' => true,
            'message' => 'Location created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location, Request $request)
    {
        if ($request->ajax()) {
            return $location;
        } else {
            return view('location.show', ['location' => $location]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'string|nullable',
            'barcode' => 'required|string',
            'location_type_id' => 'required|numeric|exists:location_types,id',
            'location_order' => 'integer|min:0|nullable',
        ]);
        $location->update($validatedData);
        return response()->json([
            'success' => true,
            'message' => 'Location updated'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     * @param Location $location
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Location $location)
    {
        if ($location->id <= 3) {
            // Location is system location, can't delete
            $success = false;
            $message = 'Location is system location';
        } elseif ($location->stock()->exists()) {
            // Location still has stock, can't delete
            $success = false;
            $message = 'Location still has stock';
        } elseif ($location->destinationTaskLines()->exists()) {
            // Location still has tasks, can't delete
            $success = false;
            $message = 'Location still has tasks';
        } else {
            $location->delete();
            $success = true;
            $message = 'Location deleted';
        }
        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    /**
     * Function for finding product through ajax post
     * @param Request $request
     * @return array
     */
    public function find(Request $request)
    {
        if ($request->input('search')) {
            return [
                'results' => Location::where('name', 'LIKE', '%' . $request->input('search') . '%')
                    ->select(['id', 'name as text'])
                    ->get()
            ];
        }
    }
}
