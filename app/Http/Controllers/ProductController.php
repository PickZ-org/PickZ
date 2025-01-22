<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('product.index', [
            'contacts' => Contact::all()
        ]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'string|nullable',
            'barcode' => 'required|string|unique:products,barcode',
            'sku' => 'string|nullable|unique:products,sku',
            'ean' => 'string|size:13|nullable',
        ];
        if($request->get('owner_contact_id')) {
            $rules['owner_contact_id'] = 'integer|exists:contacts,id';
        }
        $validatedData = $request->validate($rules);
        $newProduct = Product::create($validatedData);

        $newProduct->productUoms()->create([
            'name' => 'each',
            'quantity' => 1,
            'inbound' => true,
            'outbound' => true,
            'breakable' => false,
            'default' => true,
            'base' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request, Product $product)
    {
        if($request->ajax()) {
            return $product;
        }
        return view('product.show', [
            'product' => $product,
            'bulklocations' => Location::where([
                'location_type_id' => 1 // Bulk locations
            ])->with('fixedproductuoms')->get(),
            'picklocations' => Location::where([
                'location_type_id' => 2 // Pick locations
            ])->with('fixedproductuoms')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => 'required|string',
            'description' => 'string|nullable',
            'barcode' => 'required|string|unique:products,barcode,'.$product->id,
            'sku' => 'string|nullable|unique:products,sku,'.$product->id,
            'ean' => 'string|size:13|nullable',
            'owner_contact_id' => 'integer|exists:contacts,id|nullable'
        ];
        $validatedData = $request->validate($rules);
        $product->update($validatedData);
        return response()->json([
            'success' => true,
            'message' => 'Product updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        if($product->stocks()->exists()) {
            $success = false;
            $message = 'Product still has stock';
        } elseif($product->orderlines()->exists()) {
            $success = false;
            $message = 'Product still has orders';
        } elseif ($product->shipmentlines()->exists()) {
            $success = false;
            $message = 'Product still has shipments';
        }else {
            $product->productUoms()->delete();
            $product->logs()->delete();
            $product->meta()->delete();
            $product->delete();
            $success = true;
            $message = 'Product deleted';
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
        if($request->input('search'))
        {
            return ['results' => Product::where('name', 'LIKE', '%'.$request->input('search').'%')
                ->orWhere('sku', 'LIKE', '%'.$request->input('search').'%')
                ->orWhere('ean', 'LIKE', '%'.$request->input('search').'%')
                ->select(DB::raw("id, barcode, CONCAT(IFNULL(name, ''),' - ',IFNULL(sku, '')) as text"))
                ->get()];
        }
    }
}
