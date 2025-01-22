@extends('layouts.scanner')

@section('title', 'Select destination')

@section('icon', 'fas fa-people-carry')

@section('back-link', url('/scanner/move' ))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/move/new/destination')}}" method="post">
            @csrf
            <input type="hidden" name="source_stock_id" value="{{$sourceStock->id}}" />
            <input type="hidden" name="source_quantity" value="{{$sourceQuantity}}" />
            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Moving
                    </div>
                    <div>
                    {{$sourceQuantity}} x {{$sourceStock->product->name}} <small>({{$sourceStock->productuom->name}})</small>
                    </div>
                </div>
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        From
                    </div>
                    <div>
                        {{$sourceStock->location->name}}
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan destination location
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control scannerinput firstfocus"
                           data-filler="#location_barcode"
                           data-label="Location" data-submitter="true" />
                    <input type="hidden" name="location_barcode" id="location_barcode" value=""/>
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
