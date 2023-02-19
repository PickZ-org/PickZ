@extends('layouts.default')

@section('title', 'Order details')

@section('content')
    <div class="container-fluid">
        <div class="card card-dark">
            <div class="card-header">
                <h1 class="card-title">
                    @if($order->type->inbound)
                        <i class="fa fa-download"></i>
                    @elseif($order->type->outbound)
                        <i class="fa fa-upload"></i>
                    @endif
                    &nbsp;&nbsp;
                    {{ $order->order_no }}
                </h1>
                <div class="ribbon-wrapper ribbon-lg">
                    <div class="ribbon text-lg" style="background-color: {{ $order->status->color }};color:white;">
                        {{ $order->status->name }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <table class="table table-sm table-striped">
                            <thead>
                            <tr>
                                <th colspan="2">{{__('Order')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><i class="fas fa-fingerprint"></i> Number</td>
                                <td>{{ $order->order_no }}</td>
                            </tr>
                            <tr>
                                <td><i class="far fa-file-alt"></i> Type</td>
                                <td>
                                    {{ $order->type->name}}
                                    @if($order->type->inbound)
                                        (inbound)
                                    @elseif($order->type->outbound)
                                        (outbound)
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-search"></i> Status</td>
                                <td>
                                <span class="badge badge-pill text-white" style="background-color:{{$order->status->color}};">
                                    {{ $order->status->name }}
                                </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    @if(null !== $order->contact)
                        <div class="col-6">
                            <table class="table table-sm table-striped">
                                <thead>
                                <tr>
                                    <th colspan="2">{{__('Contact')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><i class="far fa-address-book"></i> Name</td>
                                    <td>{{$order->contact->name}}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-map-marker-alt"></i> Address</td>
                                    <td>
                                        {{$order->contact->address1}}
                                        @if($order->contact->address2)
                                            <br/>{{$order->contact->address2}}
                                        @endif
                                        @if($order->contact->address3)
                                            <br/>{{$order->contact->address3}}
                                        @endif
                                        @if($order->contact->postalcode)
                                            <br/>{{$order->contact->postalcode}}
                                        @endif
                                        @if($order->contact->city)
                                            <br/>{{$order->contact->city}}
                                        @endif
                                        @if($order->contact->state)
                                            <br/>{{$order->contact->state}}
                                        @endif
                                        @if($order->contact->country)
                                            <br/>{{$order->contact->country}}
                                        @endif
                                    </td>
                                @if($order->contact->phone)
                                    <tr>
                                        <td><i class="fa fa-phone"></i> Phone</td>
                                        <td><a class="kt-link"
                                               href="tel:{{$order->contact->phone}}">{{$order->contact->phone}}</a></td>
                                    </tr>
                                @endif
                                @if($order->contact->email)
                                    <tr>
                                        <td><i class="far fa-envelope"></i> e-mail</td>
                                        <td><a class="kt-link"
                                               href="mailto:{{$order->contact->email}}">{{$order->contact->email}}</a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                @if($order->status->id !== 99)
                    <div class="row">
                        <div class="col-lg-6">
                            <form action="{{ url('/') }}/orders/{{$order->id}}" method="POST">
                                @csrf
                                <input name="_method" type="hidden" value="PUT">
                                @switch($order->status->id)
                                    @case(10) {{--New--}}
                                    <div class="btn-group">
                                        <button type="submit" name="action" value="receive"
                                                class="btn bg-gradient-success"><i
                                                class="fa fa-truck-loading"></i> Receive order
                                        </button>
                                        <button type="button"
                                                class="btn bg-gradient-success dropdown-toggle dropdown-toggle-split"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start">
                                            <a class="dropdown-item" data-toggle="modal" href="#"
                                               data-target="#modal_partial_receive">Receive partial</a>
                                        </div>
                                    </div>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger"><i
                                            class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(81) {{--Partially received--}}
                                    <div class="btn-group">
                                        <button type="submit" name="action" value="receive"
                                                class="btn bg-gradient-success"><i
                                                class="fa fa-truck-loading"></i> Receive order
                                        </button>
                                        <button type="button"
                                                class="btn btn-success dropdown-toggle dropdown-toggle-split"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start">
                                            <a class="dropdown-item" data-toggle="modal" href="#"
                                               data-target="#modal_partial_receive">Receive partial</a>
                                        </div>
                                    </div>
                                    @break
                                    @case(82) {{--Received--}}
                                    <button type="submit" name="action" value="archive"
                                            class="btn bg-gradient-secondary"><i
                                            class="fa fa-archive"></i> Archive order
                                    </button>
                                    @break
                                    @case(90) {{--Canceled--}}
                                    <button type="submit" name="action" value="archive"
                                            class="btn bg-gradient-secondary"><i
                                            class="fa fa-archive"></i> Archive order
                                    </button>
                                    @break
                                @endswitch
                            </form>
                        </div>
                        <div class="col-6 text-right">
                            <div class="dropdown">
                                <button class="btn bg-gradient-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> Documents
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                     x-placement="bottom-start">
                                    <a class="dropdown-item" target="_blank"
                                       href="{{ url('/') }}/document/checklist/{{$order->id}}">Checklist</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h1 class="card-title">
                    Order lines
                </h1>
            </div>
            <div class="card-body">
                <table id="orderLinesTable" class="table dataTable dtr-inline"></table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">
                    Shipments
                </h1>
            </div>
            <div class="card-body">
                <table id="shipmentsTable" class="table dataTable dtr-inline"></table>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_partial_receive" tabindex="-1" role="dialog"
         aria-labelledby="modalPartialReceiveLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPartialReceiveLabel">Recieve partial</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newPartialReceiveForm"
                          action="{{ url('/') }}/orders/{{$order->id}}" method="post">
                        @csrf
                        <input name="_method" type="hidden" value="PUT">
                        <input type="hidden" name="action" value="receive-partial"/>
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    Fill in the shipment details below
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="received_date">Received
                                    on</label>
                                <div class="col-lg-9 col-md-9">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-datepicker kt-input"
                                               id="received_date"
                                               placeholder="Select date" name="received_date"
                                               value="{{date('Y-m-d')}}">
                                        <div class="input-group-append">
									<span class="input-group-text">
										<i class="fas fa-calendar"></i>
									</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h3 class="">Order lines</h3>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{__('Product')}}</th>
                                    <th>{{__('SKU')}}</th>
                                    <th>{{__('UOM')}}</th>
                                    <th>{{__('Received / Total')}}</th>
                                    <th>{{__('Open')}}</th>
                                    <th>{{__('Arrived')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->orderlines as $line)
                                    <tr>
                                        <td class="align-middle">{{$line->product->name}}</td>
                                        <td class="align-middle">{{$line->product->sku}}</td>
                                        <td class="align-middle">{{$line->productuom->name}}</td>
                                        <td class="align-middle">{{$line->processed_quantity}}
                                            / {{$line->quantity}}</td>
                                        <td class="align-middle">{{$line->quantity - $line->processed_quantity}}</td>
                                        <td class="align-middle" style="width: 100px;">
                                            <input type="hidden"
                                                   name="receive_lines[{{$loop->index}}][order_line_id]"
                                                   value="{{$line->id}}"/>
                                            <input type="number"
                                                   name="receive_lines[{{$loop->index}}][quantity]"
                                                   class="form-control" step="1" min="0"
                                                   max="{{$line->quantity - $line->processed_quantity}}"
                                                   value="{{$line->quantity - $line->processed_quantity}}"/>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit"
                            data-target="#newPartialReceiveForm" data-no-ajax="true">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $('#orderLinesTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            ajax: {
                url: '{{ url('/') }}/datatables/orderlines',
                type: 'POST',
                data: {
                    order_id: {{ $order->id }}
                },
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                }
            },
            columnDefs: [{
                defaultContent: "",
                targets: "_all"
            }],
            columns: [
                {
                    data: 'product.name',
                    name: 'product.name',
                    title: 'Product',
                },
                {
                    data: 'product.sku',
                    name: 'product.sku',
                    title: 'SKU',
                },
                {
                    data: 'product.ean',
                    name: 'product.ean',
                    title: 'EAN',
                },
                {
                    data: 'product.barcode',
                    name: 'product.barcode',
                    title: 'Barcode',
                },
                {
                    data: 'productuom.name',
                    name: 'productuom.name',
                    title: 'UOM',
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                    title: 'Quantity',
                },
                {
                    data: 'processed_quantity',
                    name: 'processed_quantity',
                    title: 'Received',
                },
            ],
        });

        $('#shipmentsTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            ajax: {
                url: '{{ url('/') }}/datatables/shipmentlines',
                type: 'POST',
                data: {
                    order_id: {{ $order->id }}
                },
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                }
            },
            columnDefs: [{
                defaultContent: "",
                targets: "_all"
            }],
            columns: [
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Date'
                },
                {
                    data: 'product.name',
                    name: 'product.name',
                    title: 'Product',
                },
                {
                    data: 'product.sku',
                    name: 'product.sku',
                    title: 'SKU',
                },
                {
                    data: 'product.ean',
                    name: 'product.ean',
                    title: 'EAN',
                },
                {
                    data: 'product.barcode',
                    name: 'product.barcode',
                    title: 'Barcode',
                },
                {
                    data: 'productuom.name',
                    name: 'productuom.name',
                    title: 'UOM',
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                    title: 'Quantity',
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                    title: 'Received by',
                },
            ],
        });
    </script>
@endpush

