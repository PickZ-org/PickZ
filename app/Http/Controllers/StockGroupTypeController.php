<?php

namespace App\Http\Controllers;

use App\Models\StockGroupType;
use http\Env\Response;
use Illuminate\Http\Request;

class StockGroupTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'name' => 'required|string',
            'enabled' => 'boolean',
            'required' => 'boolean',
            'physical' => 'boolean',
            'expires' => 'boolean',
            'specify' => 'boolean',
            'id_name' => 'required|string',
            'prefix' => 'required|string',
            'label_single' => 'required|string',
            'label_plural' => 'required|string',

        ];
        if($request->get('final_location_type_id')) {
            $rules['final_location_type_id'] = 'integer|exists:location_types,id';
        }

        $validatedData = $request->validate($rules);
        $stockGroupType = StockGroupType::create($validatedData);
        if ($stockGroupType) {
            return response()->json([
                'success' => true,
                'message' => 'Stock group type saved'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'An error occured'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param StockGroupType $stockgrouptype
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, StockGroupType $stockgrouptype)
    {
        //
        if ($request->ajax() || true) {
            return response()->json($stockgrouptype);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\StockGroupType $stockGroupType
     * @return \Illuminate\Http\Response
     */
    public function edit(StockGroupType $stockGroupType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\StockGroupType $stockGroupType
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, StockGroupType $stockgrouptype)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'enabled' => 'boolean',
            'required' => 'boolean',
            'physical' => 'boolean',
            'expires' => 'boolean',
            'specify' => 'boolean',
            'id_name' => 'required|string',
            'prefix' => 'required|string',
            'label_single' => 'required|string',
            'label_plural' => 'required|string',
            'final_location_type_id' => 'integer|exists:location_types,id|nullable'
        ]);
        if ($stockgrouptype->update($validatedData)) {
            return response()->json([
                'success' => true,
                'message' => 'Stock group type saved'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'An error occured'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StockGroupType $stockgrouptype
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StockGroupType $stockgrouptype)
    {
        if ($stockgrouptype->stockgroups()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Stock groups of this type already exist'
            ]);
        }

        if ($stockgrouptype->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock group type removed'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'An error occured while removing stock group type'
        ]);
    }
}
