@extends('layouts.scanner')

@section('title', 'Move item')

@section('icon', 'fa fa-sync-alt')

@section('back-link', url('/scanner/replenishment/' . $task->id))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/replenishment/' . $task->id . '/finish' )}}">
            @csrf
            <input type="hidden" name="product" value="{{$task->stock->product->sku}}">
            <input type="hidden" name="location" value="{{$task->destination->name}}">

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Go to
                    </div>
                    <div>
                        <strong>{{$task->destination->name}}</strong>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Drop
                    </div>
                    <div>
                        <strong>{{$task->stock->product->name}} <br /><small>({{$task->quantity}} x {{$task->stock->productuom->name}})</small></strong>
                    </div>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan Location
                    </div>
                    <div>
                        <strong>{{$task->destination->name}}</strong>
                    </div>
                </div>
                <div class="card-body">
                    <input type="text" class="form-control scannerinput firstfocus" data-validation-value="{{$task->destination->barcode}}" data-filler="#location_barcode" data-submitter="true" data-label="Location"/>
                    <input type="hidden" name="location_barcode" id="location_barcode" value="" />
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/replenishment') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Scan Location</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection
