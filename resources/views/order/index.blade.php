@extends('layouts.default')

@section('title', 'Orders')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-block bg-gradient-primary" data-toggle="modal"
                                        data-target="#modal_new_order">
                                    <i class="fa fa-cart-plus"></i>
                                    {{__('New Order')}}
                                </button>
                                @if($direction === 'inbound' && $coldstock->count() > 0)
                                    <button type="button"
                                            class="btn bg-gradient-primary dropdown-toggle dropdown-toggle-split"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" data-toggle="modal" href="#"
                                           data-target="#modal_new_coldstock_order">
                                            <i class="far fa-snowflake"></i> Cold stock order</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="orderTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_order" tabindex="-1" role="dialog" aria-labelledby="modalNewOrderLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewOrderLabel">New order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newOrderForm"
                          action="{{ url('/') }}/orders">
                        <input type="hidden" name="order_status_id" value="10"/>
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    Fill in the order details below
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="orderNo">Order
                                    number</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control" id="orderNo"
                                           autocomplete="off" name="order_no"
                                           @if (!\Configuration::get('manual_order_no'))
                                               disabled
                                           placeholder="{{__('Automatic')}}"
                                           @else
                                               placeholder="Enter order number"
                                        @endif
                                    >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="">Order
                                    type</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker ordertypeselector"
                                            name="order_type_id">
                                        @foreach( $ordertypes as $type)
                                            <option value="{{$type->id}}">{{$type->name}}
                                                ({{$type->description}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">
                                    @if($direction === 'inbound')
                                        {{__('Supplier')}}
                                    @else
                                        {{__('Destination')}}
                                    @endif
                                </label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="orderContact"
                                            data-live-search="true"
                                            name="contact_id">
                                        @foreach( $contacts as $contact)
                                            <option value="{{$contact->id}}">{{$contact->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="reqDeliveryDate">Req.
                                    delivery date</label>
                                <div class="col-lg-9 col-md-9">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-datepicker"
                                               placeholder="Select date" name="req_delivery_date">
                                        <div class="input-group-append">
									<span class="input-group-text">
										<i class="far fa-calendar"></i>
									</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row collapse crossdockcontact">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">
                                    @if($direction === 'inbound')
                                        {{__('Destination')}}
                                    @else
                                        {{__('Supplier')}}
                                    @endif
                                </label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="orderLinkedContact"
                                            data-live-search="true"
                                            name="linked_contact_id">
                                        @foreach( $contacts as $contact)
                                            <option value="{{$contact->id}}">{{$contact->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <h3 class="">Order lines</h3>
                            <div class="form-group row repeater-headers">
                                <div class="col-lg-6">
                                    Product
                                </div>
                                <div class="col-lg-2">
                                    UOM
                                </div>
                                <div class="col-lg-2">
                                    Quantity
                                </div>
                                <div class="col-lg-2"></div>
                            </div>
                            <div class="form-repeater">
                                <div data-repeater-list="orderlines">
                                    <div class="form-group row" data-repeater-item>
                                        <div class="col-lg-6">
                                            <select
                                                class="form-control select-remote-data select_product"
                                                name="product"
                                                data-remote-uri="{{ url('/') }}/products/find"></select>
                                        </div>
                                        <div class="col-lg-2">
                                            <select class="form-control select_uom"
                                                    name="productuom"
                                                    data-remote-uri="{{ url('/') }}/productuoms/find"></select>
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="number" min="0" step="1" name="quantity"
                                                   class="form-control">
                                        </div>
                                        <div class="col-lg-2">
                                            <div data-repeater-delete class="btn btn-sm bg-gradient-danger">
                                                        <span>
                                                            <i class="fas fa-trash"></i>
                                                        </span>
                                            </div>
                                            @if($direction === 'outbound' && $specifiablestockgrouptypes->isNotEmpty())
                                                <button class="btn btn-sm bg-gradient-primary btn-specify-stockgroup"
                                                        type="button">
                                                            <span>
                                                                <i class="fas fa-angle-down"></i>
                                                            </span>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="col-lg-12 collapse specify-stockgroup-collapse">
                                            <div class="row">
                                                @foreach($specifiablestockgrouptypes as $stockgrouptype)
                                                    <div class="col-lg-6">
                                                        <select
                                                            class="form-control select-remote-data specify-stockgroup"
                                                            name="stockgroup_{{$stockgrouptype->id}}"
                                                            data-placeholder="{{ucfirst($stockgrouptype->label_single)}}..."
                                                            data-remote-uri="{{ url('/') }}/stockgroups/find/{{$stockgrouptype->id}}"
                                                            data-extra=""
                                                            style="width:100%;">
                                                        </select>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <div data-repeater-create
                                             class="btn btn-sm bg-gradient-primary">
                                <span>
                                    <i class="fas fa-plus"></i>
                                    <span>Add</span>
                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#newOrderForm">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_new_coldstock_order" tabindex="-1" role="dialog"
         aria-labelledby="modalNewColdStockOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewColdStockOrderLabel">New order for current cold stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newColdStockOrderForm" action="{{ url('/') }}/orders">
                        <input type="hidden" name="order_status_id" value="10"/>
                        <input type="hidden" name="order_coldstock" value="true"/>
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    Fill in the order details below
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="orderNo">Order
                                    number</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control" id="orderNo"
                                           autocomplete="off" name="order_no"
                                           @if (!\Configuration::get('manual_order_no'))
                                               disabled
                                           placeholder="{{__('Automatic')}}"
                                           @else
                                               placeholder="Enter order number"
                                        @endif
                                    >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="">Order
                                    type</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker ordertypeselector"
                                            name="order_type_id">
                                        @foreach( $ordertypes as $type)
                                            <option value="{{$type->id}}">{{$type->name}}
                                                ({{$type->description}})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">
                                    @if($direction === 'inbound')
                                        {{__('Supplier')}}
                                    @else
                                        {{__('Destination')}}
                                    @endif
                                </label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="orderContact"
                                            data-live-search="true"
                                            name="contact_id">
                                        @foreach( $contacts as $contact)
                                            <option value="{{$contact->id}}">{{$contact->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3" for="reqDeliveryDate">Req.
                                    delivery date</label>
                                <div class="col-lg-9 col-md-9">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-datepicker"

                                               placeholder="Select date" name="req_delivery_date">
                                        <div class="input-group-append">
									<span class="input-group-text">
										<i class="far fa-calendar"></i>
									</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row collapse crossdockcontact">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">
                                    @if($direction === 'inbound')
                                        {{__('Destination')}}
                                    @else
                                        {{__('Supplier')}}
                                    @endif
                                </label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="orderLinkedContact"
                                            data-live-search="true"
                                            name="linked_contact_id">
                                        @foreach( $contacts as $contact)
                                            <option value="{{$contact->id}}">{{$contact->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <h3>Order lines</h3>
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="coldstockcheckall"
                                               checked="checked"/>
                                    </th>
                                    <th>{{__('Product')}}</th>
                                    <th>{{__('SKU')}}</th>
                                    <th>{{__('UOM')}}</th>
                                    <th>{{__('Quantity')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($coldstock as $stock)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   name="coldstocks[{{$loop->iteration}}][id]"
                                                   value="{{$stock->id}}" checked="checked"
                                                   class="coldstockrowremove"
                                                   data-max-quantity="{{$stock->quantity}}"/>
                                        </td>
                                        <td>{{$stock->product->name}}</td>
                                        <td>{{$stock->product->sku}}</td>
                                        <td>{{$stock->productuom->name}}</td>
                                        <td>
                                            <input type="number"
                                                   name="coldstocks[{{$loop->iteration}}][quantity]" min="0"
                                                   max="{{$stock->quantity}}" value="{{$stock->quantity}}"
                                                   step="1" name="quantity"
                                                   class="form-control csquantity">
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
                            data-target="#newColdStockOrderForm">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $datatable = $('#orderTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/orders',
                type: 'POST',
                data: {
                    direction: '{{ $direction }}'
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
                    data: null,
                    defaultContent: '',
                    className: 'select-checkbox',
                    orderable: false,
                },
                {
                    data: null,
                    defaultContent: '<i class="fas fa-plus-circle"></i>',
                    className: 'details-control',
                    orderable: false,
                },
                {
                    data: 'order_no',
                    name: 'order_no',
                    title: 'Order no',
                    render: function (data, type, row, meta) {
                        return '<a class="" href="{{ url('/') }}/orders/' + row.id + '">' + row.order_no + '</a>';
                    }
                },
                {
                    data: 'orderlines_count',
                    name: 'orderlines_count',
                    title: 'Lines',
                },
                {
                    data: 'status.name',
                    name: 'status.name',
                    title: 'Status',
                    render: function (data, type, row, meta) {
                        return '<span class="badge" style="background-color:' + row.status.color + ';color:#ffffff;">' + row.status.name + '</span>';
                    }
                },
                {
                    data: 'type.name',
                    name: 'type.name',
                    title: 'Order type',
                },
                {
                    data: 'contact.name',
                    name: 'contact.name',
                    title: 'Contact',
                },
                {
                    data: 'req_delivery_date',
                    name: 'req_delivery_date',
                    title: 'RDD',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Created',
                },
            ],
            order: [
                [2, 'asc']
            ],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    text: 'Bulk actions',
                    split:
                        [
                                @switch($direction)
                                @case('inbound')
                            {
                                text: 'Receive now',
                                action: function (e, dt) {
                                    bulkAction(dt.rows({selected: true}).data(), 'receive');
                                }
                            },
                                @break
                                @case('outbound')
                            {
                                text: 'Start / procees',
                                action: function (e, dt) {
                                    bulkAction(dt.rows({selected: true}).data(), 'start');
                                }
                            },
                            {
                                text: 'Batchpick',
                                action: function (e, dt) {
                                    bulkAction(dt.rows({selected: true}).data(), 'batch-pick');
                                }
                            },
                            {
                                text: 'Confirm shipment',
                                action: function (e, dt) {
                                    bulkAction(dt.rows({selected: true}).data(), 'confirm-shipment');
                                }
                            },
                                @break
                                @endswitch
                            {
                                text: 'Archive',
                                action: function (e, dt) {
                                    bulkAction(dt.rows({selected: true}).data(), 'archive');
                                }
                            },
                        ],
                }
            ]
        });

        $datatable.on('click', 'td.details-control', function () {
            let tr = $(this).closest('tr');
            let row = $datatable.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                $(this).html('<i class="fas fa-plus-circle"></i>')
            } else {
                row.child(subTable(row.data()), 'child-row').show();
                $(this).html('<i class="fas fa-minus-circle"></i>')
            }
        });

        function subTable(data) {
            var div = $('<div/>').addClass('loading').text('Loading...');
            $.ajax({
                url: '{{ url('/') }}/datatables/orderlines',
                method: 'POST',
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                data: {
                    order_id: data.id
                },
                dataType: 'json',
                success: function (json) {
                    let subTable = $('<table/>').addClass('table').addClass('table-sm');
                    let subTableHeaders = $('<thead/>')
                        .append($('<th/>').html('Product'))
                        .append($('<th/>').html('SKU'))
                        .append($('<th/>').html('EAN'))
                        .append($('<th/>').html('Qty'))
                        .append($('<th/>').html('UOM'));
                    let subTableBody = $('<tbody/>');
                    $.each(json.data, function (key, item) {
                        let row = $('<tr/>')
                            .append($('<td/>').html(item.product.name))
                            .append($('<td/>').html(item.product.sku))
                            .append($('<td/>').html(item.product.ean))
                            .append($('<td/>').html(item.quantity))
                            .append($('<td/>').html(item.productuom.name));
                        subTableBody.append(row);
                    });
                    subTable.append(subTableHeaders).append(subTableBody);
                    div.html(subTable).removeClass('loading');
                }
            });

            return div;
        }

        function bulkAction(rows, action) {
            let ids = [];
            $(rows).each(function (index, value) {
                ids.push(value.id);
            });
            $.ajax({
                url: "{{ url('/orders') }}/bulk",
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'POST',
                dataType: 'JSON',
                data: {
                    action: action,
                    ids: ids
                },
                success: function (data) {
                    if (data.success) {
                        // Success
                        toastr.success(data.message, 'Success');
                    } else {
                        // Show error info
                        toastr.error(data.message, 'Error');
                    }
                    $datatable.ajax.reload();
                },
                error: function (response) {
                    if (typeof response.responseJSON.message !== 'undefined') {
                        toastr.error(response.responseJSON.message, 'Error');
                    } else {
                        toastr.error('An unknown error occurred', 'Error');
                    }
                }
            });
        }

        $('.form-repeater').on('addline', function (e) {
            $('.select-remote-data.select_product').on('select2:select', function (e) {
                let data = e.params.data;
                $uom_select = $(this).closest('.form-group').find('.select_uom');
                $specify_stockgroup = $(this).closest('.form-group').find('.specify-stockgroup');
                if ($specify_stockgroup !== undefined) {
                    $specify_stockgroup.data('extra', $(this).val());
                }
                $.ajax({
                    type: "POST",
                    url: "{{ url('/productuoms/findbyproduct') }}",
                    headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                    data: {
                        'product_id': data.id,
                        'direction': '{{$direction}}',
                    },
                    success: function (response) {
                        $uom_select.empty();
                        for (var i = 0; i < response.length; i++) {
                            let id = response[i]['id'];
                            let name = response[i]['name'];
                            $option = $uom_select.append("<option value='" + id + "'>" + name + "</option>");
                            if (response[i]['default'] == '1') {
                                $uom_select.val(id)
                                $option.prop('selected', 'selected');
                            }
                        }
                    },
                    dataType: 'JSON'
                });
            });
            $('.btn-specify-stockgroup').on('click', function () {
                $(this).parents('.row').find('.specify-stockgroup-collapse').collapse('toggle');
            });
        });

        $('.ordertypeselector').on('change', function () {
            let $crossdockcontact = $(this).closest('form').find('.crossdockcontact');
            if ($(this).val() === '7' || $(this).val() === '6') { // Crossdock order types, show linked order inputs
                $crossdockcontact.collapse('show');
            } else {
                $crossdockcontact.collapse('hide');
            }
        });

        $('.coldstockcheckall').on('change', function () {
            if ($(this).prop('checked')) {
                $('.coldstockrowremove').prop('checked', true);
            } else {
                $('.coldstockrowremove').prop('checked', false);
            }
        });

    </script>
@endpush
