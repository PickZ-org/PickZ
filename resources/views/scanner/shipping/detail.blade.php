@extends('layouts.scanner')

@section('title', 'Scan item')

@section('icon', 'fas fa-shipping-fast ')

@section('back-link', url('/scanner/shipping'))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/shipping/' . $task->id  )}}">
            @csrf
            <input type="hidden" name="product" value="{{$taskline->stock->product->sku}}">
            <input type="hidden" name="taskline" value="{{$taskline->id}}">

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Go to
                    </div>
                    <div>
                        <strong>{{$taskline->stock->location->name}}</strong>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Pick up
                    </div>
                    <div>
                        <strong>{{$taskline->stock->product->name}} <br /><small>({{$taskline->quantity}} x {{$taskline->stock->productuom->name}})</small></strong>
                    </div>
                </div>
                @foreach($taskline->stock->stockgroups as $stockGroup)
                    <div class="card-body d-flex w-100 justify-content-between">
                        <div>
                            {{$stockGroup->type->label_single}}
                        </div>
                        <div>
                            <strong>{{$stockGroup->group_no}}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan item
                    </div>
                    <div>
                        <strong>{{$taskline->stock->product->name}}</strong>
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control scannerinput firstfocus" data-validation-value="{{$taskline->stock->product->barcode}}" data-filler="#product_barcode" data-next-focus="#quantity" data-label="Barcode"/>
                    <input type="hidden" name="product_barcode" id="product_barcode" value="" />
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body text-center">
                    <p>
                        Enter quantity <small>({{$taskline->quantity}} x {{$taskline->stock->productuom->name}})</small>
                    </p>
                    <input type="number" pattern="\d*"step="1" id="quantity" name="quantity" class="form-control kt-input scannerinput" data-validation-value="{{$taskline->quantity}}" data-submitter="true" data-label="Quantity">
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/shipping') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
