<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('contact.index');
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $validatedData = $request->validate([
            'name' => 'required|string',
            'address1' => 'nullable|string',
            'address2' => 'nullable|string',
            'address3' => 'nullable|string',
            'postalcode' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'phone' => 'nullable|numeric',
            'email' => 'nullable|e-mail',
        ]);

        try {
            Contact::create($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Contact created'
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
        return $contact;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|string',
            'address1' => 'nullable|string',
            'address2' => 'nullable|string',
            'address3' => 'nullable|string',
            'postalcode' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
            'phone' => 'nullable|numeric',
            'email' => 'nullable|e-mail',
        ]);

        try {
            $contact->update($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'Contact updated'
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->errorInfo
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Contact $contact
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Contact $contact)
    {
        // TODO: Check for (open) orders by contact before making this possible
        if($contact->orders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Contact already has orders'
            ]);
        } elseif ($contact->ownerproducts()->exists()) {
            $products = $contact->ownerproducts;
            return response()->json([
                'success' => false,
                'message' => 'Contact is linked to products: ' . $products->implode('name', ', ')
            ]);
        } else {
            $contact->delete();
            return response()->json([
                'success' => true,
                'message' => 'Contact removed'
            ]);
        }
    }
}
