@extends('layouts.scanner')

@section('title', 'Receive cold')

@section('icon', 'fas fa-dolly')

@section('back-link', url('/scanner/receiving'))

@section('content')
    <div class="col-12">
        <form action="{{url('/scanner/receiving/cold')}}" method="POST" id="sendForm">
            @csrf
            <div class="card mb-2">
                <div class="card-body d-flex w-100 justify-content-between">
                    <div>
                        Scan item
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="input-group">
                            <input id="product" type="text" class="form-control firstfocus scannerinput"
                                   data-filler="#barcode"
                                   data-validation-value="" data-next-focus="#quantity" data-label="Barcode"
                                   data-uom-selector="#product_uom"/>
                            <input type="hidden" name="product_barcode" id="barcode" value=""/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <a data-toggle="modal" href="#modal_new_product"><i class="fa fa-plus"></i></a>
                                </span>
                                <span class="input-group-text">
                                    <a data-toggle="modal" href=".lookup-modal"><i class="fa fa-search"></i></a>
                                </span>
                            </div>
                        </div>
                        <small class="form-text text-muted display-product-name">&nbsp;</small>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        Enter quantity
                    </p>
                    <div class="form-group">
                        <input type="number" pattern="\d*" step="1" min="1" id="quantity" name="quantity"
                               class="form-control kt-input" data-validation-value=""
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
                        <a href="{{ url('/scanner/receiving') }}" class="btn btn-danger">Cancel</a>
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
    <div class="modal fade" id="modal_new_product" tabindex="false" role="dialog" aria-labelledby="modalNewProductLabel"
         style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewProductLabel">{{ __('New Product') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="kt-form kt-form--fit kt-form--label-right" id="newProductForm"
                          action="{{ url('/') }}/products">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                <div class="form-group kt-form__group">
                                    <div class="alert alert-primary" role="alert">
                                        {{ __('Fill in the Product details below') }}
                                    </div>
                                </div>

                                <div
                                    class="form-group kt-form__group row{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <div class="col-lg-9 col-md-9 col-skt-12">
                                        <input type="text" class="form-control kt-input"
                                               placeholder="{{ __('Name') }}" autocomplete="off" name="name"
                                               value="{{ old('name') }}" required>
                                        @if ($errors->has('name'))
                                            <div class="form-control-feedback">{{ $errors->first('name') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="form-group kt-form__group row{{ $errors->has('sku') ? ' has-danger' : '' }}">
                                    <div class="col-lg-9 col-md-9 col-skt-12">
                                        <input type="text" class="form-control kt-input"
                                               placeholder="{{ __('SKU') }}" autocomplete="off" name="sku"
                                               value="{{ old('sku') }}" required>
                                        @if ($errors->has('sku'))
                                            <div class="form-control-feedback">{{ $errors->first('sku') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="form-group kt-form__group row{{ $errors->has('ean') ? ' has-danger' : '' }}">
                                    <div class="col-lg-9 col-md-9 col-skt-12">
                                        <input type="number" min="1" step="1" class="form-control kt-input"
                                               placeholder="{{ __('EAN') }}" autocomplete="off" name="ean"
                                               value="{{ old('ean') }}" required>
                                        @if ($errors->has('ean'))
                                            <div class="form-control-feedback">{{ $errors->first('ean') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div
                                    class="form-group kt-form__group row{{ $errors->has('barcode') ? ' has-danger' : '' }}">
                                    <div class="col-lg-9 col-md-9 col-skt-12">
                                        <input type="text" class="form-control kt-input"
                                               placeholder="{{ __('Barcode') }}" autocomplete="off" name="barcode"
                                               value="{{ old('barcode') }}" required>
                                        @if ($errors->has('barcode'))
                                            <div class="form-control-feedback">{{ $errors->first('barcode') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-success btn-modal-form-submit" data-target="#newProductForm">
                        {{ __('Save product') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $('.btn-modal-form-submit').click(function () {
            const $form = $($(this).data('target'));
            const $modal = $form.closest('.modal');

            if ($(this).data('no-ajax')) {
                $form.submit();
            } else {
                $.ajax({
                    type: $form.attr('method') || 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: $form.attr('action'), // the url where we want to POST
                    data: $form.serialize(), // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true,
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken
                    },
                    success: function (data) {
                        $modal.modal('hide');
                        if (data.success) {
                            // Success
                            let barcode = $form.find('input[name="barcode"]').val();
                            $('#product').val(barcode);
                            $('#product').trigger('input');
                            fill_uom_selector($('#lookup_stock_uom'), barcode);
                            toastr.success(data.message, 'Success');
                        } else {
                            // Show error info
                            toastr.error(data.message, 'Error');
                        }
                    },
                    error: function (response) {
                        $modal.modal('hide');
                        if (typeof response.responseJSON.message !== 'undefined') {
                            let errorMsg = response.responseJSON.message;
                            if (typeof response.responseJSON.errors !== 'undefined') {
                                for (var property in response.responseJSON.errors) {
                                    errorMsg = errorMsg + '<br />' + response.responseJSON.errors[property][0];
                                }
                            }
                            toastr.error(errorMsg, 'Error');
                        } else {
                            toastr.error('An unknown error occurred', 'Error');
                        }
                    }
                });
            }
        });
    </script>
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
    <script>
        $('.select-stockgroup').on('select2:select', function (e) {
            if (e.params.data.newTag) {
                // Expiry date group is new, enable expiry date field
                $('.' + $(this).data('expiryinput')).prop('disabled', false);
            } else {
                $('.' + $(this).data('expiryinput')).prop('disabled', true);
            }
        });
    </script>
@endpush


@push('scripts')
    @if(\Configuration::get('zebra_printing'))
        @include('scanner.includes.zebraprinterscript')
    @else
        @include('scanner.includes.scannerlabel')
    @endif
@endpush

