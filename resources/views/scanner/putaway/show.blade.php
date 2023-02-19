@extends('layouts.scanner')

@section('title', 'Pick up item')

@section('icon', 'fa fa-dolly-flatbed ')

@section('back-link', url('/scanner/putaway' ))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/putaway/' . $task->id . '/move' )}}">
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
                        <strong>{{$task->stock->product->name}} <br/><small>({{$task->quantity}}
                                x {{$task->stock->productuom->name}})</small></strong>
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
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control scannerinput firstfocus" id="product"
                                   data-validation-value="{{$task->stock->product->barcode}}"
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
                        Enter quantity <small>({{$task->quantity}} x {{$task->stock->productuom->name}})</small>
                    </p>
                    <input type="number" pattern="\d*" step="1" id="quantity" name="quantity"
                           class="form-control kt-input scannerinput" data-validation-value="{{$task->quantity}}"
                           data-submitter="true" data-label="Quantity">
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/putaway') }}" class="btn btn-danger">Cancel</a>
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

@push('scripts')
    @include('scanner.includes.lookupscript')
@endpush
