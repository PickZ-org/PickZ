<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\ProductUom;
use Illuminate\Http\Request;

class ProductUomController extends Controller
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
        $validatedData = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'inbound' => 'required|boolean',
            'outbound' => 'required|boolean',
            'breakable' => 'required|boolean',
            'bulk_pick' => 'required|boolean',
            'price_unit' => 'numeric|nullable',
            'price_period' => 'numeric|nullable',
        ]);
        $productUom = ProductUom::create($validatedData);

        if ($request->get('default', false)) {
            $productUom->setDefault();
        }
        return response()->json([
            'success' => true,
            'message' => 'UOM saved'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ProductUom $productuom
     * @return ProductUom
     */
    public function show(Request $request, ProductUom $productuom)
    {
        if ($request->ajax()) {
            return $productuom;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ProductUom $productuom)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'inbound' => 'required|boolean',
            'outbound' => 'required|boolean',
            'breakable' => 'required|boolean',
            'bulk_pick' => 'required|boolean',
            'price_unit' => 'numeric|nullable',
            'price_period' => 'numeric|nullable',
        ]);
        if ($productuom->base && $validatedData['quantity'] > 1) {
            $success = false;
            $message = "Can't update base UOM quantity";
        } elseif ($productuom->base && $validatedData['breakable'] === '1') {
            $success = false;
            $message = "Can't make base UOM breakable";
        } else {
            $productuom->update($validatedData);
            if ($request->get('default')) {
                $productuom->setDefault();
            }
            $success = true;
            $message = 'UOM updated';
        }
        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param ProductUom $productuom
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(ProductUom $productuom)
    {
        if ($productuom->base) {
            $success = false;
            $message = 'UOM is base UOM';
        } elseif ($productuom->orderlines()->exists()) {
            $success = false;
            $message = 'UOM still has orders';
        } elseif ($productuom->tasklines()->exists()) {
            $success = false;
            $message = 'UOM still has tasks';
        } else {
            if ($productuom->default) {
                // Set base UOM to default
                ProductUom::where([
                    'base' => 1,
                    'product_id' => $productuom->product_id
                ])->first()->setDefault();
            }
            $productuom->delete();
            $success = true;
            $message = 'UOM deleted';
        }
        return response()->json([
            'success' => $success,
            'message' => $message
        ]);
    }

    /**
     * Find UOMs by providing product ID
     * @param Request $request
     * @return ProductUom[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByProduct(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'direction' => 'required|string|in:inbound,outbound'
        ]);
        return ProductUom::whereHas('product', function ($query) use ($validatedData) {
            $query->where('id', '=', $validatedData['product_id']);
        })->where([
            $validatedData['direction'] => true
        ])->get();
    }

    /**
     * Find UOMs by providing product Barcode (usually scanner)
     * @param Request $request
     * @return ProductUom[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByBarcode(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'required|string|exists:products,barcode',
            'direction' => 'required|string|in:inbound,outbound'
        ]);
        return ProductUom::with('product')->whereHas('product', function ($query) use ($validatedData) {
            $query->where('barcode', '=', $validatedData['barcode']);
        })->where([
            $validatedData['direction'] => true
        ])->get();
    }

    /**
     * For attaching fixed locations to UOMs
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addFixedLocation(Request $request)
    {
        $validatedData = $request->validate([
            'product_uom_id' => 'required|exists:product_uoms,id',
            'location_id' => 'required|exists:locations,id',
        ]);
        $pivotData = $request->validate([
            'minimum_quantity' => 'integer|min:0|nullable',
            'top_up_quantity' => 'integer|min:1|nullable',
            'maximum_quantity' => 'integer|min:1|nullable',
            'auto_replenish' => 'required|boolean'
        ]);

        $productUom = ProductUom::findOrFail($validatedData['product_uom_id']);
        $location = Location::findOrFail($validatedData['location_id']);

        if ($location->type->id !== 2) {
            // Not a pick locaiton, remove auto replenish options
            $pivotData['minimum_quantity'] = null;
            $pivotData['top_up_quantity'] = null;
            $pivotData['auto_replenish'] = false;
        }

        if ($productUom->fixedlocations()->find($location->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Fixed location already exists'
            ]);
        }

        $productUom->fixedlocations()->attach($location, $pivotData);

        return response()->json([
            'success' => true,
            'message' => 'Fixed location added'
        ]);
    }

    /**
     * For detaching fixed locations from UOMs
     * @param ProductUom $productuom
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFixedLocation(ProductUom $productuom, Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:locations,id'
        ]);
        $record = $productuom->fixedlocations()->detach(Location::findOrFail($validatedData['id']));
        return response()->json([
            'success' => true,
            'message' => 'Fixed location removed'
        ]);
    }

    /**
     * Show specific fixed location with pivot values and this product UOM
     * @param ProductUom $productuom
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getFixedLocation(ProductUom $productuom, Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:locations,id'
        ]);
        $record = $productuom->fixedlocations()->with([
            'fixedproductuoms' => function ($query) use ($productuom) {
                $query->where(['product_uoms.id' => $productuom->id]);
            }
        ])->find($validatedData['id']);
        return $record;
    }

    /**
     * Update specific fixed location for UOM
     * @param ProductUom $productuom
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFixedLocation(ProductUom $productuom, Request $request)
    {
        $validatedData = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'old_location_id' => 'required|exists:locations,id',
            'product_uom_id' => 'required|exists:product_uoms,id',
            'old_product_uom_id' => 'required|exists:product_uoms,id',
        ]);

        $pivotData = $request->validate([
            'minimum_quantity' => 'integer|min:0|nullable',
            'top_up_quantity' => 'integer|min:1|nullable',
            'maximum_quantity' => 'integer|min:1|nullable',
            'auto_replenish' => 'required|boolean'
        ]);

        // Check new data
        $newProductUom = ProductUom::findOrFail($validatedData['product_uom_id']);
        $location = Location::findOrFail($validatedData['location_id']);
        $oldProductUom = ProductUom::findOrFail($validatedData['old_product_uom_id']);
        $oldLocation = Location::findOrFail($validatedData['old_location_id']);

        if ($location->type->id !== 2) {
            // Not a pick locaiton, remove auto replenish options
            $pivotData['minimum_quantity'] = null;
            $pivotData['top_up_quantity'] = null;
            $pivotData['auto_replenish'] = false;
        }

        if ($oldProductUom->id === $newProductUom->id && $oldLocation->id === $location->id) {
            // Just update the pivot attributes
            $oldProductUom->fixedlocations()->updateExistingPivot($oldLocation->id, $pivotData);
            return response()->json([
                'success' => true,
                'message' => 'Fixed location updated'
            ]);
        }

        if ($newProductUom->fixedlocations()->find($location->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Fixed location already exists'
            ]);
        }

        // Detach old data
        $productuom->fixedlocations()->detach(Location::findOrFail($validatedData['old_location_id']));

        $newProductUom->fixedlocations()->attach($location, $pivotData);

        return response()->json([
            'success' => true,
            'message' => 'Fixed location updated'
        ]);


    }
}
