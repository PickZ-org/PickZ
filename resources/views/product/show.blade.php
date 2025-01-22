@extends('layouts.default')

@section('title', 'Product: ' . $product->name )

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-dark">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-box"></i> {{ $product->name }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-striped">
                            <thead>
                            <tr>
                                <th colspan="2">{{__('Product')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><i class="fa fa-hashtag"></i> ID</td>
                                <td>{{ $product->id }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-hashtag"></i> SKU</td>
                                <td>{{ $product->sku }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-hashtag"></i> EAN</td>
                                <td>{{ $product->ean }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-barcode"></i> Barcode</td>
                                <td>{{ $product->barcode }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-signature"></i> Name</td>
                                <td>{{ $product->name}}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-align-justify"></i> Description</td>
                                <td>{{ $product->description }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-calendar-alt"></i> Created at</td>
                                <td>{{ $product->created_at }}</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-calendar-alt"></i> Updated at</td>
                                <td>{{ $product->updated_at }}</td>
                            </tr>
                            @if( count( $product->meta ) )
                            </tbody>
                            <thead>
                            <tr>
                                <th colspan="2">{{__('Product Meta')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ( $product->meta as $meta )
                                <tr>
                                    <td>{{$meta->key}}</td>
                                    <td>{{$meta->value}}</td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">
                            <i class="fa fa-ruler-combined"></i> {{__('Units of measurement')}}
                        </h1>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-block bg-gradient-primary" data-toggle="modal"
                                        data-target="#modal_new_uom">
                                    <i class="fas fa-plus"></i> {{__('New UOM')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="uomTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-map-pin"></i> {{__('Fixed locations')}}
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-block bg-gradient-primary" data-toggle="modal"
                                        data-target="#modal_new_fixed_location">
                                    <i class="fas fa-plus"></i> {{__('New fixed location')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="fixedlocationsTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_uom" tabindex="false" role="dialog" aria-labelledby="modalNewUomLabel"
         style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewUomLabel">{{ __('New UOM') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newUomForm"
                          action="{{ url('/') }}/productuoms">
                        <input type="hidden" value="{{$product->id}}" name="product_id"/>
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the UOM details below') }}
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('UOM') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('UOM') }}" autocomplete="off" name="name"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="1" step="1"
                                           placeholder="{{ __('Quantity') }}" autocomplete="off" name="quantity"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Price / unit') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="0" step=".01"
                                           placeholder="{{ __('Price / unit') }}" autocomplete="off"
                                           name="price_unit"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Price / period') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="0" step=".01"
                                           placeholder="{{ __('Price / period') }}" autocomplete="off"
                                           name="price_unit"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Default') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="default"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="default"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Inbound') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="inbound"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="inbound"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Outbound') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="outbound"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="outbound"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Breakable') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="breakable"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="breakable"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Pick from bulk') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="bulk_pick"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="bulk_pick"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-success btn-modal-form-submit" data-target="#newUomForm">
                        {{ __('Save UOM') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_uom" tabindex="-1" role="dialog" aria-labelledby="modalEditUomLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditUomLabel">{{ __('New UOM') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editUomForm"
                          action="{{ url('/') }}/productuoms" method="put">
                        <input type="hidden" value="{{$product->id}}" name="product_id"/>
                        <div class="container-fluid">

                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the UOM details below') }}
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('UOM') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('UOM') }}" autocomplete="off" name="name"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="1" step="1"
                                           placeholder="{{ __('Quantity') }}" autocomplete="off" name="quantity"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Price / unit') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="0" step=".01"
                                           placeholder="{{ __('Price / unit') }}" autocomplete="off"
                                           name="price_unit"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Price / period') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="0" step=".01"
                                           placeholder="{{ __('Price / period') }}" autocomplete="off"
                                           name="price_period"
                                           value="" required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Default') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="default"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="default"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Inbound') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="inbound"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="inbound"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Outbound') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="outbound"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="outbound"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Breakable') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="breakable"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="breakable"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Pick from bulk') }}</label>
                                <div class="col-lg-9 pt-2">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden"
                                               name="bulk_pick"
                                               value="0"/>
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               name="bulk_pick"
                                               value="1">
                                        <label class="custom-control-label"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#editUomForm">
                        {{ __('Update UOM') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_new_fixed_location" tabindex="false" role="dialog"
         aria-labelledby="modalNewFixedLocationLabel"
         style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewFixedLocationLabel">{{ __('New fixed location') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newFixedLocationForm"
                          action="{{ url('/') }}/productuoms/fixedlocations" method="post">
                        <div class="container-fluid">

                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the fixed location details below') }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">{{__('UOM')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="product_uom_id"
                                            name="product_uom_id">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">{{__('Location')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker location-selector" id="location_id"
                                            name="location_id">
                                        <option value=""></option>
                                        <optgroup label="{{__('Bulk')}}"/>
                                        @foreach($bulklocations as $location)
                                            <option
                                                value="{{$location->id}}">{{$location->name}} @if ($location->fixedproductuoms->isNotEmpty())
                                                    ({{__('used')}})
                                                @endif </option>
                                        @endforeach
                                        <optgroup label="{{__('Pick')}}"/>
                                        @foreach($picklocations as $location)
                                            <option
                                                value="{{$location->id}}">{{$location->name}} @if ($location->fixedproductuoms->isNotEmpty())
                                                    ({{__('used')}})
                                                @endif </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Max. quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="0" step="1"
                                           placeholder="{{ __('Max. quantity') }}" autocomplete="off"
                                           name="maximum_quantity"
                                           value="">

                                </div>
                            </div>
                            <div class="replenishment-collapse collapse">
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Auto replenishment') }}</label>
                                    <div class="col-lg-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="auto_replenish"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="auto_replenish"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Min. quantity') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="number" class="form-control kt-input" min="0" step="1"
                                               placeholder="{{ __('Min. quantity') }}" autocomplete="off"
                                               name="minimum_quantity"
                                               value="">

                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Top up quantity') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="number" class="form-control kt-input" min="0" step="1"
                                               placeholder="{{ __('Top up quantity') }}" autocomplete="off"
                                               name="top_up_quantity"
                                               value="">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit"
                            data-target="#newFixedLocationForm">
                        {{ __('Save fixed location') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_fixed_location" tabindex="false" role="dialog"
         aria-labelledby="modalEditFixedLocationLabel"
         style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditFixedLocationLabel">{{ __('Edit fixed location') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editFixedLocationForm"
                          action="{{ url('/') }}/productuoms/fixedlocations" method="put">
                        <input type="hidden" name="old_product_uom_id" id="old_product_uom_id"/>
                        <input type="hidden" name="old_location_id" id="old_location_id"/>
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the fixed location details below') }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">{{__('UOM')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker" id="edit_product_uom_id"
                                            name="product_uom_id">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="exampleSelect1">{{__('Location')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker location-selector"
                                            id="edit_location_id"
                                            name="location_id">
                                        <option value=""></option>
                                        <optgroup label="{{__('Bulk')}}"/>
                                        @foreach($bulklocations as $location)
                                            <option
                                                value="{{$location->id}}">{{$location->name}} @if ($location->fixedproductuoms->isNotEmpty())
                                                    ({{__('used')}})
                                                @endif </option>
                                        @endforeach
                                        <optgroup label="{{__('Pick')}}"/>
                                        @foreach($picklocations as $location)
                                            <option
                                                value="{{$location->id}}">{{$location->name}} @if ($location->fixedproductuoms->isNotEmpty())
                                                    ({{__('used')}})
                                                @endif </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Max. quantity') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" class="form-control kt-input" min="0" step="1"
                                           placeholder="{{ __('Max. quantity') }}" autocomplete="off"
                                           name="maximum_quantity"
                                           value="">

                                </div>
                            </div>
                            <div class="replenishment-collapse collapse">
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Auto replenishment') }}</label>
                                    <div class="col-lg-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="auto_replenish"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="auto_replenish"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Min. quantity') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="number" class="form-control kt-input" min="0" step="1"
                                               placeholder="{{ __('Min. quantity') }}" autocomplete="off"
                                               name="minimum_quantity"
                                               value="">

                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Top up quantity') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="number" class="form-control kt-input" min="0" step="1"
                                               placeholder="{{ __('Top up quantity') }}" autocomplete="off"
                                               name="top_up_quantity"
                                               value="">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit"
                            data-target="#editFixedLocationForm">
                        {{ __('Update fixed location') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        function renderBool(value) {
            if (value === 1 || value === true) {
                return "<i class='fas fa-check text-success'></i>"
            } else {
                return "<i class='fas fa-times text-danger'></i>"
            }
        }

        /**
         * UOM table
         */
        $uom_table = $('#uomTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/productuoms',
                type: 'POST',
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                },
                data: {
                    product_id: {{ $product->id }},
                }
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
                },
                {
                    data: 'quantity',
                    name: 'quantity',
                    title: 'Quantity',
                },
                {
                    data: 'base',
                    name: 'base',
                    title: 'Base UOM',
                    render: function (data, type, row, meta) {
                        return renderBool(data);
                    },
                },
                {
                    data: 'default',
                    name: 'default',
                    title: 'Default',
                    render: function (data, type, row, meta) {
                        return renderBool(data);
                    },
                },
                {
                    data: 'inbound',
                    name: 'inbound',
                    title: 'Inbound',
                    render: function (data, type, row, meta) {
                        return renderBool(data);
                    },
                },
                {
                    data: 'outbound',
                    name: 'outbound',
                    title: 'Outbound',
                    render: function (data, type, row, meta) {
                        return renderBool(data);
                    },
                },
                {
                    data: 'breakable',
                    name: 'breakable',
                    title: 'Breakable',
                    render: function (data, type, row, meta) {
                        return renderBool(data);
                    },
                },
                {
                    data: 'bulk_pick',
                    name: 'bulk_pick',
                    title: 'Pick from bulk',
                    render: function (data, type, row, meta) {
                        return renderBool(data);
                    },
                },
                {
                    data: 'price_unit',
                    name: 'price_unit',
                    title: 'Price / unit',
                },
                {
                    data: 'price_period',
                    name: 'price_period',
                    title: 'Price / period',
                },
                {
                    title: 'Actions',
                    name: 'actions',
                    width: '100px',
                    render: function (data, type, row, meta) {
                        return '<a href="#" data-target="' + row.id + '" class="editUom btn btn-default btn-xs btn-table" title="Edit"><i class="far fa-edit"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="deleteUom btn btn-danger btn-xs btn-table" title="Delete"><i class="far fa-trash-alt"></i></a>';
                    },
                    sortable: false
                },
            ],
            autoWidth: false
        });

        $uom_table.on('click', 'tr td a.deleteUom', function (e) {
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
                        url: "{{ url('/productuoms' ) }}/" + id,
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
                            $uom_table.ajax.reload();
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

        $uom_table.on('click', 'tr td a.editUom', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/productuoms' ) }}/" + id,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#modal_edit_uom form').attr('action', '{{ url('/') }}/productuoms/' + id);
                    $('#modal_edit_uom form input:not([type=hidden])').each(function (index, element) {
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
                    $('#modal_edit_uom form select').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                            $(element).selectpicker('refresh');
                        }
                    });
                    $('#modal_edit_uom').modal('show');
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

        /**
         * Fixed locations table
         */
        $fixedlocations_table = $('#fixedlocationsTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/fixedlocations',
                type: 'POST',
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                },
                data: {
                    product_id: {{ $product->id }},
                }
            },
            columnDefs: [{
                defaultContent: "",
                targets: "_all"
            }],
            columns: [
                {
                    data: 'fixedproductuoms.0.name',
                    name: 'fixedproductuoms.0.name',
                    title: 'UOM',
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Location',
                },
                {
                    data: 'type.name',
                    name: 'type.name',
                    title: 'Type',
                },
                {
                    data: 'pivot.maximum_quantity',
                    name: 'pivot.maximum_quantity',
                    title: 'Max. quantity',
                },
                {
                    data: 'pivot.minimum_quantity',
                    name: 'pivot.minimum_quantity',
                    title: 'Min. quantity',
                },
                {
                    data: 'pivot.top_up_quantity',
                    name: 'pivot.top_up_quantity',
                    title: 'Top up quantity',
                },
                {
                    data: 'pivot.auto_replenish',
                    name: 'pivot.auto_replenish',
                    title: 'Auto replenishment',
                    render: function (data, type, row, meta) {
                        if (row.type.id === 2) { // Pick locations only
                            if (row.pivot.auto_replenish === 1 || row.pivot.auto_replenish === true) {
                                return renderBool(true);
                            } else {
                                return renderBool(false);
                            }
                        }
                    }
                },
                {
                    title: 'Actions',
                    name: 'actions',
                    width: '100px',
                    render: function (data, type, row, meta) {
                        return '<a href="#" data-product-uom-id="' + row.fixedproductuoms[0].id + '" data-target="' + row.id + '" class="editFixedLocation btn btn-xs btn-default btn-table" title="Edit"><i class="far fa-edit"></i></a>' +
                            '<a href="#" data-product-uom-id="' + row.fixedproductuoms[0].id + '" data-target="' + row.id + '" class="deleteFixedLocation btn btn-xs btn-danger btn-table" title="Delete"><i class="far fa-trash-alt"></i></a>';
                    },
                    sortable: false
                },
            ],
            autoWidth: false
        });

        $fixedlocations_table.on('click', 'tr td a.deleteFixedLocation', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            let uomId = $(this).data('product-uom-id');
            swal.fire({
                title: '{{__('Are you sure?')}}',
                text: "You won't be able to revert this",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it'
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('/productuoms/fixedlocations' ) }}/" + uomId,
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
                            $fixedlocations_table.ajax.reload();
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

        $('#modal_new_fixed_location').on('shown.bs.modal', function (e) {
            fill_uom_selector($('#product_uom_id'), {{$product->id}});
        });

        $fixedlocations_table.on('click', 'tr td a.editFixedLocation', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            let uomId = $(this).data('product-uom-id');

            $('#old_product_uom_id').val(uomId);
            $('#old_location_id').val(id);

            $.ajax({
                url: "{{ url('/productuoms/fixedlocations' ) }}/" + uomId,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    fill_uom_selector($('#edit_product_uom_id'), {{$product->id}}, data.fixedproductuoms[0].id);
                    $('#modal_edit_fixed_location form').attr('action', '{{ url('/') }}/productuoms/fixedlocations/' + uomId);
                    $('#modal_edit_fixed_location form input:not([type=hidden])').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data.pivot[name]) != 'undefined') {
                            $(element).val(data.pivot[name]);
                            if ($(element).attr('type') === 'checkbox') {
                                $(element).val(1); // Checkboxes should be one and sent depending on checked
                                if (data.pivot[name] === 1) {
                                    // check checkbox
                                    $(element).prop('checked', true);
                                } else {
                                    // uncheck checkbox
                                    $(element).prop('checked', false);
                                }
                            }
                        }
                    });
                    $('#edit_location_id').val(data.id);
                    $('#modal_edit_fixed_location form select').each(function (index, element) {
                        // $(element).selectpicker('refresh');
                    });
                    $('#modal_edit_fixed_location').modal('show');
                    if (data.location_type_id === 2) { // Show replenishment options if it's a pick location
                        $('.replenishment-collapse').collapse('show')
                    } else {
                        $('.replenishment-collapse').collapse('hide')
                    }
                },
                error: function (response) {
                    if (typeof response.responseJSON.message !== 'undefined') {
                        toastr.error(response.responseJSON.message, 'Error');
                    } else {
                        toastr.error('An unknown error occurred', 'Error');
                    }
                }
            });
        });

        $('.location-selector').on('change', function (e) {
            let label = $(this).find(':selected').parent().attr('label');
            if (label === 'Pick') {
                $('.replenishment-collapse').collapse('show')
            } else {
                $('.replenishment-collapse').collapse('hide')
            }
        });
    </script>
@endpush
