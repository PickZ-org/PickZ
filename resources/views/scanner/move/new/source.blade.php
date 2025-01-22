@extends('layouts.scanner')

@section('title', 'Move item')

@section('icon', 'fas fa-people-carry')

@section('back-link', url('/scanner/move' ))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/move/new/source')}}" method="post">
            @csrf
            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan source location
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control scannerinput firstfocus"
                           data-filler="#location_barcode"
                           data-label="Location" data-next-focus="#product"/>
                    <input type="hidden" name="location_barcode" id="location_barcode" value=""/>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan item
                    </div>
                </div>
                <div class="card-body">
                    <input id="product" type="text" class="form-control scannerinput"
                           data-filler="#barcode"
                           data-validation-value="" data-next-focus="#quantity" data-label="Barcode"
                           data-uom-selector="#product_uom"/>
                    <input type="hidden" name="barcode" id="barcode" value=""/>
                    <small class="form-text text-muted display-product-name"></small>
                </div>
                <div class="card-body">
                    <p>
                        Enter quantity
                    </p>
                    <div class="form-group">
                        <input type="number" pattern="\d*" step="1" min="1" id="quantity" name="quantity"
                               class="form-control kt-input" data-validation-value=""
                               data-label="Quantity">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="product_uom" id="product_uom">

                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/move') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Move stock</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
