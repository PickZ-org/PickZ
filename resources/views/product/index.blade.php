@extends('layouts.default')

@section('title', 'Products')

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
                                        data-target="#modal_new_product">
                                    <i class="fa fa-plus"></i>
                                    {{__('New Product')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="productTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('modals')
    <div class="modal fade" id="modal_new_product" tabindex="-1" role="dialog" aria-labelledby="modalNewProductLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewProductLabel">{{ __('New Product') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newProductForm" action="{{ url('/') }}/products">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Product details below') }}
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Name') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Name') }}" autocomplete="off" name="name"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Description') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Description') }}" autocomplete="off"
                                           name="description"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('SKU') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('SKU') }}" autocomplete="off" name="sku"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('EAN') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" min="1" step="1" class="form-control"
                                           placeholder="{{ __('EAN') }}" autocomplete="off" name="ean"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Barcode') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Barcode') }}" autocomplete="off" name="barcode"
                                           value="" required>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">{{__('Product owner')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="ownerContact"
                                            data-live-search="true"
                                            name="owner_contact_id">
                                        <option value=""></option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#newProductForm">
                        {{ __('Save product') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_product" tabindex="-1" role="dialog"
         aria-labelledby="modalEditProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditProductLabel">{{ __('Edit Product') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editProductForm"
                          action="{{ url('/') }}/products" method="put">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Product details below') }}
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Name') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Name') }}" autocomplete="off" name="name"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Description') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Description') }}" autocomplete="off"
                                           name="description"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('SKU') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('SKU') }}" autocomplete="off" name="sku"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('EAN') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" min="1" step="1" class="form-control"
                                           placeholder="{{ __('EAN') }}" autocomplete="off" name="ean"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Barcode') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Barcode') }}" autocomplete="off" name="barcode"
                                           value="" required>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">{{__('Product owner')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="ownerContact"
                                            data-live-search="true"
                                            name="owner_contact_id">
                                        <option value=""></option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#editProductForm">
                        {{ __('Update product') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $table = $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/products',
                type: 'POST',
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                },
            },
            columnDefs: [{
                defaultContent: "",
                targets: "_all"
            }],
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    title: 'Name',
                    render: function (data, type, row, meta) {
                        return '<a class="kt-link" href="{{ url('/') }}/products/' + row.id + '">' + row.name + '</a>';
                    },
                },
                {
                    data: 'description',
                    name: 'description',
                    title: 'Description',
                },
                {
                    data: 'sku',
                    name: 'sku',
                    title: 'SKU',
                },
                {
                    data: 'ean',
                    name: 'ean',
                    title: 'EAN',
                },
                {
                    data: 'barcode',
                    name: 'barcode',
                    title: 'Barcode',
                },
                {
                    title: 'Actions',
                    name: 'actions',
                    width: '100px',
                    render: function (data, type, row, meta) {
                        return '<a href="#" data-target="' + row.id + '" class="editProduct btn btn-default btn-xs btn-table" title="Edit"><i class="far fa-edit"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="productLabel btn btn-default btn-xs btn-table" title="Create label"><i class="fas fa-barcode"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="deleteProduct btn btn-danger btn-xs btn-table" title="Delete"><i class="far far fa-trash-alt"></i></a>';
                    },
                    sortable: false
                },
            ],
            autoWidth: false
        });

        $table.on('click', 'tr td a.deleteProduct', function (e) {
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
                        url: "{{ url('/products' ) }}/" + id,
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

        $table.on('click', 'tr td a.editProduct', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/products' ) }}/" + id,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#modal_edit_product form').attr('action', '{{ url('/') }}/products/' + id);
                    $('#modal_edit_product form input').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                        }
                    });
                    $('#modal_edit_product form select').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                            // $(element).selectpicker('refresh');
                        }
                    });
                    $('#modal_edit_product').modal('show');
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

        $table.on('click', 'tr td a.productLabel', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            window.open("{{ url('/') }}/document/productlabel/" + id);
        });
    </script>
@endpush
