<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->user()->hasRole('guest')) {
            // return stock that belongs to this user
            $user = $request->user();
            if (null !== $user->contact) {
                $return = [];
                $stocks = Stock::with(['location', 'productuom', 'product'])->whereHas('product.owner', function ($query) use ($user) {
                    $query->where(['id' => $user->contact->id]);
                })->get();
                foreach ($stocks as $stock) {
                    $return[] = [
                        'location' => $stock->location->name,
                        'product' => $stock->product->name,
                        'uom' => $stock->productuom->name,
                        'quantity' => $stock->quantity
                    ];
                }
                return response()->json($return);
            } else {
                return response()->json([
                    'error' => 'User has no contact'
                ]);
            }
        } else {
            // return all stock
            return Stock::all();
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Stock $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        //
    }
}
