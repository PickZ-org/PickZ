@extends('layouts.scanner')

@section('title', 'Scan item')

@section('icon', 'fas fa-arrows-alt-h')

@section('back-link', url('/scanner/crossdock'))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/crossdock/' . $task->id  )}}">
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
                        <strong>{{$taskline->stock->product->name}} <br/><small>({{$taskline->quantity}}
                                x {{$taskline->stock->productuom->name}})</small></strong>
                    </div>
                </div>
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
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control scannerinput firstfocus" id="product"
                                   data-validation-value="{{$taskline->stock->product->barcode}}"
                                   data-filler="#product_barcode"
                                   data-next-focus="#quantity" data-label="Barcode"/>
                            <input type="hidden" name="product_barcode" id="product_barcode" value=""/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <a data-toggle="modal" href=".lookup-modal"><i class="fa fa-search"></i></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card mb-2">
                <div class="card-body text-center">
                    <p>
                        Enter quantity <small>({{$taskline->quantity}} x {{$taskline->stock->productuom->name}})</small>
                    </p>
                    <input type="number" pattern="\d*" step="1" id="quantity" name="quantity"
                           class="form-control kt-input scannerinput" data-validation-value="{{$taskline->quantity}}"
                           data-submitter="true" data-label="Quantity">
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/crossdock') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target=".order-modal">
                            Order
                        </button>
                    </div>
                    <div>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" style="">
                                <a class="dropdown-item" href="{{url('/scanner/crossdock/' . $task->id  . '/drop')}}">
                                    Drop off
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('modals')
    @include('scanner.includes.lookupmodal')
@endpush

@push('modals')
    @include('scanner.includes.ordermodal')
@endpush

@push('scripts')
    @include('scanner.includes.lookupscript')
@endpush
