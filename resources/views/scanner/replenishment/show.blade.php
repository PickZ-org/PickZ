@extends('layouts.scanner')

@section('title', 'Pick up item')

@section('icon', 'fa fa-sync-alt')

@section('back-link', url('/scanner/replenishment' ))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/replenishment/' . $task->id . '/move' )}}">
            @csrf
            <input type="hidden" name="product" value="{{$task->stock->product->sku}}">

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Go to
                    </div>
                    <div>
                        <strong>{{$task->stock->location->name}}</strong>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Pick up
                    </div>
                    <div>
                        <strong>{{$task->stock->product->name}} <br /><small>({{$task->quantity}} x {{$task->stock->productuom->name}})</small></strong>
                    </div>
                </div>
                @foreach($task->stock->stockgroups as $stockGroup)
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
                        <strong>{{$task->stock->product->name}}</strong>
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control scannerinput firstfocus" data-validation-value="{{$task->stock->product->barcode}}" data-filler="#product_barcode" data-next-focus="#quantity" data-label="Barcode"/>
                    <input type="hidden" name="product_barcode" id="product_barcode" value="" />
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body text-center">
                    <p>
                        Enter quantity <small>({{$task->quantity}} x {{$task->stock->productuom->name}})</small>
                    </p>
                    <input type="number" pattern="\d*"step="1" id="quantity" name="quantity" class="form-control kt-input scannerinput" data-validation-value="{{$task->quantity}}" data-submitter="true" data-label="Quantity">
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/replenishment') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
