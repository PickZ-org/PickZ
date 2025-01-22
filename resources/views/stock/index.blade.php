@extends('layouts.default')

@section('title', 'Stock')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                        </h3>
                        @if(Auth::user()->hasRole(['admin', 'manager']))
                            <div class="card-tools">
                                <a href="#" class="btn btn-block bg-gradient-primary" data-toggle="modal"
                                   data-target="#modal_new_stock">
                                    <span>
                                    <i class="fas fa-plus-circle"></i>
                                    <span>{{ __('Add Stock') }}</span>
                                    </span>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <table id="stockTable" class="table table-striped dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_stock" tabindex="-1" role="dialog" aria-labelledby="modalNewStockLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalNewStockLabel">{{ __('Add Stock') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="newStockForm" action="{{ url('/') }}/stock">
                        <div class="container-fluid">
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="alert alert-primary" role="alert">
                                        {{ __('Fill in the Stock details below') }}
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('Location')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            name="location_id">
                                        @foreach( $bulkLocations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('Product')}}</label>
                                <div class="col-lg-9">
                                    <select class="form-control select-remote-data"
                                            name="product_id"
                                            id="new_stock_product_id"
                                            data-remote-uri="{{ url('/') }}/products/find"
                                            style="width:100%;"></select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('UOM')}}</label>
                                <div class="col-lg-9">
                                    <select class="form-control selectpicker"
                                            name="product_uom_id" id="new_stock_uom">
                                        <option value="0"></option>
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" min="1" step="1" class="form-control"
                                           placeholder="{{ __('Quantity') }}" autocomplete="off" name="quantity"
                                           value="" required>
                                </div>
                            </div>
                            @foreach ($stockgrouptypes as $stockgrouptype)
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __(ucfirst($stockgrouptype->label_single)) }}</label>
                                    @if ($stockgrouptype->expires)
                                        <div class="col-6">
                                            <select
                                                class="form-control select-remote-data select-stockgroup"
                                                data-tags="true"
                                                data-searchdata="{{$stockgrouptype->id}}"
                                                data-idcolumn="group_no"
                                                data-expiryinput="input-expirydate-{{$loop->iteration}}"
                                                name="stockgrouptype[{{$stockgrouptype->id}}][group_no]"
                                                data-remote-uri="{{ url('/') }}/stockgroups/find"
                                                style="width:100%;"></select>
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="text"
                                                   class="form-control form-datepicker input-expirydate-{{$loop->iteration}}"
                                                   disabled
                                                   placeholder="Expiry date"
                                                   name="stockgrouptype[{{$stockgrouptype->id}}][expiry_date]"
                                                   value=""/>
                                        </div>
                                    @else
                                        <div class="col-lg-9">
                                            <select class="form-control select-remote-data"
                                                    data-tags="true"
                                                    data-searchdata="{{$stockgrouptype->id}}"
                                                    data-idcolumn="group_no"
                                                    name="stockgrouptype[{{$stockgrouptype->id}}][group_no]"
                                                    data-remote-uri="{{ url('/') }}/stockgroups/find"
                                                    style="width:100%;"></select>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#newStockForm">
                        {{ __('Save stock') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_stock" tabindex="-1" role="dialog"
         aria-labelledby="modalEditStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditStockLabel">{{ __('Adjust Stock') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editStockForm" action="{{ url('/') }}/stocks" method="put">
                        <div class="container-fluid">
                            <div class="form-group ">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Stock details below') }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('Location')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            disabled
                                            name="location_id">
                                        @foreach( $allLocations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('Product')}}</label>
                                <div class="col-lg-9">
                                    <select class="form-control select-remote-data"
                                            name="product_id" disabled
                                            data-remote-uri="{{ url('/') }}/products/find"
                                            style="width:100%;"></select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('UOM')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            id="edit_stock_uom"
                                            disabled
                                            name="product_uom_id">
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" min="1" step="1" class="form-control kt-input"
                                           placeholder="{{ __('Quantity') }}" autocomplete="off" name="quantity"
                                           value="{{ old('quantity') }}" required>
                                    @if ($errors->has('quantity'))
                                        <div class="form-control-feedback">{{ $errors->first('quantity') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label">{{__('Blocked')}}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="blocked"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="blocked"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            @foreach ($stockgrouptypes as $stockgrouptype)
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __(ucfirst($stockgrouptype->label_single)) }}</label>
                                    @if ($stockgrouptype->expires)
                                        <div class="col-lg-6">
                                            <select
                                                class="form-control select-remote-data select-stockgroup"
                                                data-tags="true"
                                                data-searchdata="{{$stockgrouptype->id}}"
                                                data-idcolumn="group_no"
                                                data-expiryinput="edit-expirydate-{{$loop->iteration}}"
                                                name="stockgrouptype[{{$stockgrouptype->id}}][group_no]"
                                                data-remote-uri="{{ url('/') }}/stockgroups/find"
                                                style="width:100%;"></select>
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="text"
                                                   class="form-control form-datepicker edit-expirydate-{{$loop->iteration}}"
                                                   disabled
                                                   placeholder="Expiry date"
                                                   name="stockgrouptype[{{$stockgrouptype->id}}][expiry_date]"
                                                   value=""/>
                                        </div>
                                    @else
                                        <div class="col-lg-9">
                                            <select class="form-control select-remote-data"
                                                    data-tags="true"
                                                    data-searchdata="{{$stockgrouptype->id}}"
                                                    data-idcolumn="group_no"
                                                    name="stockgrouptype[{{$stockgrouptype->id}}][group_no]"
                                                    data-remote-uri="{{ url('/') }}/stockgroups/find"
                                                    style="width:100%;"></select>
                                        </div>

                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#editStockForm">
                        {{ __('Update stock') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_move_stock" tabindex="-1" role="dialog"
         aria-labelledby="modalMoveStockLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMoveStockLabel">{{ __('Move Stock') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="moveStockForm" action="{{ url('/') }}/stocks/move" method="post">
                        <div class="container-fluid">
                            <div class="form-group ">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the move details below') }}
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('Destination')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            name="location_id">
                                        @foreach( $allLocations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" min="1" step="1" class="form-control"
                                           placeholder="{{ __('Quantity') }}" autocomplete="off" name="quantity"
                                           value="{{ old('quantity') }}" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="move_direct">{{ __('') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="hidden" name="move_direct" value="0"/>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="move_direct"
                                               value="1" id="move_direct">
                                        <label for="move_direct"
                                            class="form-check-label">{{ __('Move stock directly (skip move task)') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#moveStockForm">
                        {{ __('Move stock') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            const $table = $('#stockTable').dataTable({
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                responsive: true,
                ajax: {
                    url: '{{ url('/') }}/datatables/stock',
                    type: 'POST',
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
                        data: 'location.name',
                        name: 'location.name',
                        title: 'Location',
                    },
                    {
                        data: 'product.name',
                        name: 'product.name',
                        title: 'Product',
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
                        data: 'order.order_no',
                        name: 'order.order_no',
                        title: 'Order',
                    },
                        @foreach($stockgrouptypes as $type)
                    {
                        data: 'stockgroups.{{ $type->id }}',
                        name: 'stockgroups.{{ $type->id }}',
                        title: '{{ ucfirst($type->label_single) }}',
                        render: function (data, type, row, meta) {
                            let group_no = '';
                            row.stockgroups.forEach(function (item, index) {
                                if (item.stock_group_type_id === {{$type->id}}) {
                                    group_no = item.group_no;
                                    if (item.expiry_date !== undefined && item.expiry_date !== '' && item.expiry_date !== null) {
                                        group_no += ' (' + item.expiry_date + ')';
                                    }
                                }
                            });
                            return group_no;
                        }
                    },
                        @endforeach
                    {
                        data: 'blocked',
                        name: 'blocked',
                        title: 'Blocked',
                        render: function (data, type, row, meta) {
                            if (row.blocked === 1) {
                                return '<i class="fas fa-ban text-danger"></i>';
                            }
                        }
                    },
                        @if(Auth::user()->hasRole(['admin','manager']))
                    {
                        title: 'Actions',
                        name: 'actions',
                        render: function (data, type, row, meta) {
                            return '<a href="#" data-target="' + row.id + '" class="editStock btn btn-default btn-xs btn-table" title="Edit stock"><i class="far fa-edit"></i></a>' +
                                '<a href="#" data-target="' + row.id + '" class="moveStock btn btn-default btn-xs btn-table" title="Move stock"><i class="fas fa-people-carry"></i></a>' +
                                '<a href="#" data-target="' + row.id + '" class="stockLabel btn btn-default btn-xs btn-table" title="Create label"><i class="fas fa-barcode"></i></a>';
                        },
                        sortable: false
                    }
                    @endif
                ],
            });

            $table.on('click', 'tr td a.deleteStock', function (e) {
                e.preventDefault();
                let id = $(this).data('target');

                swal.fire({
                    title: '{{__('Are you sure?')}}',
                    text: "You won't be able to revert this",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it'
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{ url('/stock' ) }}/" + id,
                            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                            type: 'DELETE',
                            dataType: 'JSON',
                            data: {
                                id: id
                            },
                            success: function (data) {
                                if (data.success) {
                                    // Success
                                    toastr.success(data.message, 'Success');
                                } else {
                                    // Show error info
                                    toastr.error(data.message, 'Error');
                                }
                                $table.reload();
                            },
                            error: function (response) {
                                if (typeof response.responseJSON.message !== 'undefined') {
                                    toastr.error(response.responseJSON.message, 'Error');
                                } else {
                                    toastr.error('An unknown error occurred', 'Error');
                                }
                            }
                        })
                    }
                });
            });

            $table.on('click', 'tr td a.editStock', function (e) {
                e.preventDefault();
                let id = $(this).data('target');

                $.ajax({
                    url: "{{ url('/stock' ) }}/" + id,
                    headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#modal_edit_stock form').attr('action', '{{ url('/') }}/stock/' + id);
                        $('#modal_edit_stock form input:not([type=hidden])').each(function (index, element) {
                            const name = $(element).attr('name');
                            if (typeof (data[name]) != 'undefined') {
                                $(element).val(data[name]);
                                if ($(element).attr('type') === 'checkbox') {
                                    $(element).val(1); // Checkboxes should be one and sent depending on checked
                                    if (data[name] === 1 || data[name] === true) {
                                        // check checkbox
                                        $(element).prop('checked', true);
                                    } else {
                                        // uncheck checkbox
                                        $(element).prop('checked', false);
                                    }
                                }
                            }
                        });
                        $('#modal_edit_stock form select.selectpicker').each(function (index, element) {
                            const name = $(element).attr('name');
                            if (typeof (data[name]) != 'undefined') {
                                $(element).val(data[name]);
                            }
                        });
                        $('#modal_edit_stock form select.select-remote-data').each(function (index, element) {
                            const name = $(element).attr('name');
                            // Clear the select
                            $(element).empty().trigger('change');
                            if (typeof (data[name]) != 'undefined') {
                                let newOption = new Option(data.product.name, data[name], true, true);
                                // Append it to the select
                                $(element).append(newOption).trigger('change');
                            }
                        });
                        for (var i = 0; i < data.stockgroups.length; i++) {
                            let elemName = 'stockgrouptype[' + data.stockgroups[i].stock_group_type_id + '][group_no]';
                            if ($('[name="' + elemName + '"]').length) {
                                let element = $('[name="' + elemName + '"]');
                                let newOption = new Option(data.stockgroups[i].group_no, data.stockgroups[i].group_no, true, true);
                                // Append it to the select
                                element.empty().trigger('change');
                                element.append(newOption).trigger('change');
                            }
                            elemName = 'stockgrouptype[' + data.stockgroups[i].stock_group_type_id + '][expiry_date]';
                            if ($('[name="' + elemName + '"]').length) {
                                let element = $('[name="' + elemName + '"]');
                                element.val(data.stockgroups[i].expiry_date);
                            }
                        }
                        fill_uom_selector($('#edit_stock_uom'), data.product.id);
                        $('#modal_edit_stock').modal('show');
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

            $table.on('click', 'tr td a.moveStock', function (e) {
                e.preventDefault();
                let id = $(this).data('target');

                $.ajax({
                    url: "{{ url('/stock' ) }}/" + id,
                    headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#modal_move_stock form').attr('action', '{{ url('/') }}/stock/move/' + id);
                        $('#modal_move_stock form input').each(function (index, element) {
                            const name = $(element).attr('name');
                            if (typeof (data[name]) != 'undefined') {
                                $(element).val(data[name]);
                            }
                        });
                        $('#modal_move_stock form select.selectpicker').each(function (index, element) {
                            const name = $(element).attr('name');
                            if (typeof (data[name]) != 'undefined') {
                                $(element).val(data[name]);
                            }
                        });
                        $('#modal_move_stock form select.select-remote-data').each(function (index, element) {
                            const name = $(element).attr('name');
                            if (typeof (data[name]) != 'undefined') {
                                var newOption = new Option(data.product.name, data[name], true, true);
                                // Append it to the select
                                $(element).append(newOption).trigger('change');
                            }
                        });
                        fill_uom_selector($('#move_stock_uom'), data.product.id);
                        $('#modal_move_stock').modal('show');
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

            $('#new_stock_product_id').on('change', function () {
                fill_uom_selector($('#new_stock_uom'), $(this).val());
            });

            $('.select-stockgroup').on('select2:select', function (e) {
                $('.' + $(this).data('expiryinput')).val('');
                if (e.params.data.newTag) {
                    // Expiry date group is new, enable expiry date field
                    $('.' + $(this).data('expiryinput')).prop('disabled', false);
                } else {
                    $('.' + $(this).data('expiryinput')).prop('disabled', true);
                }
            });

            $table.on('click', 'tr td a.stockLabel', function (e) {
                e.preventDefault();
                let id = $(this).data('target');
                window.open("{{ url('/') }}/document/stocklabel/" + id);
            });
        });
    </script>
@endpush
