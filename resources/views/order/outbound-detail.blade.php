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
                                <span class="badge badge-pill text-white"
                                      style="background-color:{{$order->status->color}};">
                                    {{ $order->status->name }}
                                </span>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-lock"></i> Stock reserved</td>
                                <td>
                                    @if($order->stockreservations->count())
                                        <span class="badge badge-success">Yes</span>
                                    @else
                                        <span class="badge badge-danger">No</span>
                                    @endif
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
                        <div class="col-6">
                            <form action="{{ url('/') }}/orders/{{$order->id}}" method="POST">
                                @csrf
                                <input name="_method" type="hidden" value="PUT">
                                @switch($order->status->id)
                                    @case(10) {{--New--}}
                                    <button type="submit" name="action" value="start-order"
                                            class="btn bg-gradient-success">
                                        <i class="fa fa-play"></i> Start order
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="flaticon-circle"></i> Cancel order
                                    </button>
                                    @break
                                    @case(20) {{--Ready for replenishment--}}
                                    <button type="submit" name="action" value="start-replenishment"
                                            class="btn bg-gradient-success">
                                        <i class="fas fa-hands"></i> Start replenishment
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(21) {{--Ready for picking--}}
                                    <button type="submit" name="action" value="start-picking"
                                            class="btn bg-gradient-success">
                                        <i class="fa fa-box-open"></i> Start picking
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(22) {{--Ready for shipment--}}
                                    <button type="submit" name="action" value="ship" class="btn bg-gradient-success">
                                        <i class="fas fa-shipping-fast"></i> Confirm shipment
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(30) {{--In picking--}}
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(31) {{--In replenishment--}}
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(32) {{--In staging--}}
                                    <button type="submit" name="action" value="start-shipment"
                                            class="btn bg-gradient-success">
                                        <i class="fa fa-truck-loading"></i> Start shipping
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(33) {{--In movement--}}
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(50) {{--Need stock--}}
                                    <button type="submit" name="action" value="check-stock"
                                            class="btn bg-gradient-warning">
                                        <i class="fa fa-box"></i> Check stock
                                    </button>
                                    <button type="submit" name="action" value="cancel" class="btn bg-gradient-danger">
                                        <i class="fas fa-ban"></i> Cancel order
                                    </button>
                                    @break
                                    @case(80) {{--Completed--}}
                                    <button type="submit" name="action" value="archive"
                                            class="btn bg-gradient-secondary">
                                        <i class="fa fa-archive"></i> Archive order
                                    </button>
                                    @break
                                    @case(90) {{--Canceled--}}
                                    <button type="submit" name="action" value="archive"
                                            class="btn bg-gradient-secondary">
                                        <i class="fa fa-archive"></i> Archive order
                                    </button>
                                    <button type="submit" name="action" value="open" class="btn bg-gradient-success">
                                        <i class="fa fa-folder-open"></i> Re-open order
                                    </button>
                                    @break
                                @endswitch
                            </form>
                        </div>
                        <div class="col-lg-6">
                            <div class="dropdown dropdown-inline float-right ml-3">
                                <button class="btn bg-gradient-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-print"></i> Documents
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                     x-placement="bottom-start">
                                    <a class="dropdown-item @if($order->status->id != 30) disabled @endif"
                                       target="_blank"
                                       href="{{ url('/') }}/document/picklist/{{$order->id}}"><i
                                            class="fas fa-hands"></i> Pick list</a>
                                    <a class="dropdown-item" target="_blank"
                                       href="{{ url('/') }}/document/deliverynote/{{$order->id}}"><i
                                            class="fas fa-truck-loading"></i> Delivery note</a>
                                </div>
                            </div>
                            @if(\Configuration::get('invoicing', false))
                                <div class="dropdown dropdown-inline float-right">
                                    <button class="btn bg-gradient-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-file-invoice-dollar"></i> New invoice
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"
                                         x-placement="bottom-start">
                                        <a class="dropdown-item createinvoice" target="_blank" href="#"
                                           data-invoicetype="sales"><i
                                                class="fas fa-shopping-cart"></i> Sales
                                            invoice</a>
                                        <a class="dropdown-item createinvoice @if($order->status->id != 80) disabled @endif"
                                           href="#" data-invoicetype="storage"><i
                                                class="far fa-hourglass"></i> Storage invoice</a>
                                    </div>
                                </div>
                            @endif
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

@push('scripts')
    <script>
        $(document).ready(function () {
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
                        @foreach($stockgrouptypes as $type)
                    {
                        data: "stockgroups.{{$type->id}}",
                        name: "stockgroups.{{$type->id}}",
                        title: "{{ucfirst($type->label_single)}}",
                        sortable: false,
                        render: function (data, type, row, meta) {
                            let group_no = '';
                            row.stockgroups.forEach(function (item, index) {
                                if (item.stock_group_type_id == {{$type->id}}) {
                                    group_no = item.group_no;
                                }
                            });
                            return group_no;
                        },
                    },
                        @endforeach
                    {
                        data: 'quantity',
                        name: 'quantity',
                        title: 'Quantity',
                    },
                    {
                        data: 'processed_quantity',
                        name: 'processed_quantity',
                        title: 'Sent',
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

            $('.createinvoice').click(function (e) {
                e.preventDefault();
                var type = $(this).data('invoicetype');
                $.ajax({
                    url: "{{ url('/invoices') }}",
                    headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        type: type,
                        order_id: '{{$order->id}}'
                    },
                    success: function (data) {
                        if (data.success) {
                            // Success
                            toastr.success(data.message, 'Success');
                        } else {
                            // Show error info
                            toastr.error(data.message, 'Error');
                        }
                    },
                    error: function (response) {
                        if (typeof response.responseJSON.message !== 'undefined') {
                            toastr.error(response.responseJSON.message, 'Error');
                        } else {
                            toastr.error('An unknown error occurred', 'Error');
                        }
                    }
                })
            });
        });
    </script>
@endpush

