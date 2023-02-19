@extends('layouts.scanner')

@section('title', 'Receive order ' . $order->order_no)

@section('icon', 'fas fa-dolly')

@section('back-link', url('/scanner/receiving/order'))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/receiving/order/' . $order->id )}}" id="sendForm" method="POST">
            @csrf
            <input type="hidden" name="order_id" value="{{$order->id}}">
            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan item
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="input-group">
                            <input id="product" type="text"
                                   name="barcode"
                                   data-filler="#barcode"
                                   class="form-control scannerinput firstfocus"
                                   data-validation-value="" data-uom-selector="#product_uom"
                                   data-next-focus="#quantity" data-label="Barcode"/>
                            <input type="hidden" name="product_barcode" id="barcode" value=""/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <a data-toggle="modal" href=".lookup-modal"><i class="fa fa-search"></i></a>
                                </span>
                            </div>
                        </div>
                        <small class="form-text text-muted display-product-name"></small>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Enter quantity
                    </p>
                    <div class="form-group">
                        <input type="number" pattern="\d*" step="1" id="quantity"
                               name="quantity"
                               class="form-control kt-input scannerinput"
                               data-label="Quantity">
                    </div>
                    <div class="form-group">
                        <select class="form-control" name="product_uom" id="product_uom">

                        </select>
                    </div>
                </div>
                @include('scanner.includes.stockgroups')
            </div>

            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        <a href="{{ url('/scanner/receiving/order') }}" class="btn btn-danger">Cancel</a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target=".items-modal">
                            Items
                        </button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary"
                                onclick="printStockLabel()">
                            Label
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success">Receive</button>
                    </div>
                </div>
            </div>

        </form>
    </div>
@endsection

@push('modals')
    <div class="modal fade items-modal" tabindex="-1" role="dialog" aria-labelledby="ItemsModal"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Open lines for order: {{$order->order_no}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        @foreach($order->orderlines as $line)
                            @if($line->open_quantity > 0)
                                <li class="list-group-item">
                                    <strong>{{$line->product->name}} </strong>
                                    <br/><small>({{$line->open_quantity}} x {{$line->productuom->name}})</small>
                                    <br/><small>SKU: {{$line->product->sku}}</small>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    @include('scanner.includes.lookupmodal')
@endpush

@push('scripts')
    @include('scanner.includes.lookupscript')
@endpush

@push('scripts')
    @include('scanner.includes.stockgroupscript')
@endpush

@push('scripts')
    @if(\Configuration::get('zebra_printing'))
        @include('scanner.includes.zebraprinterscript')
    @else
        @include('scanner.includes.scannerlabel')
    @endif
@endpush
