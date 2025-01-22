@extends('layouts.scanner')

@section('title', 'Move item')

@section('icon', 'fas fa-arrows-alt-h')

@section('back-link', url('/scanner/crossdock/' . $task->id))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/crossdock/' . $task->id . '/drop' )}}">
            @csrf
            <input type="hidden" name="product" value="{{$taskline->stock->product->sku}}">
            <input type="hidden" name="taskline" value="{{$taskline->id}}">

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Go to
                    </div>
                    <div>
                        <strong>{{$taskline->destination->name}}</strong>
                    </div>
                </div>
                @if(!$locationverified)
                    <div class="card-body">
                        <input type="text" class="form-control scannerinput firstfocus"
                               data-validation-value="{{$taskline->destination->barcode ?? ''}}"
                               data-filler="#location_barcode" data-label="Location" data-next-focus="#product"/>
                        <input type="hidden" name="location_barcode" id="location_barcode" value=""/>
                    </div>
                @else
                    <input type="hidden" name="location_barcode" id="location_barcode"
                           value="{{$taskline->destination->barcode ?? '1'}}"/>
                @endif
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Drop
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
                        Scan Item
                    </div>
                    <div>
                        <strong>{{$taskline->stock->product->name}}</strong>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" id="product"
                                   class="form-control scannerinput @if($locationverified) firstfocus @endif"
                                   data-validation-value="{{$taskline->stock->product->barcode}}"
                                   data-filler="#product_barcode" data-submitter="true" data-label="Barcode"/>
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
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/crossdock') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target=".order-modal">
                            Order info
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
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
