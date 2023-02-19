@extends('layouts.default')

@section('title', 'Configuration')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#settings_tab_1" role="tab">
                                    <i class="fas fa-code-branch"></i> {{ __('Workflow') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " data-toggle="tab" href="#settings_tab_2" role="tab">
                                    <i class="fas fa-robot"></i> {{ __('Automation') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " data-toggle="tab" href="#settings_tab_3" role="tab">
                                    <i class="fas fa-boxes"></i> {{ __('Stock') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " data-toggle="tab" href="#settings_tab_4" role="tab">
                                    <i class="fas fa-file-invoice-dollar"></i> {{ __('Invoicing') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " data-toggle="tab" href="#settings_tab_5" role="tab">
                                    <i class="fas fa-print"></i> {{ __('Printing') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!--begin::form-->
                    <form class="" id="configurationForm" action="{{url('/configuration')}}">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="settings_tab_1" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="pick_from_bulk" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="pick_from_bulk" id="pick_from_bulk" value="1"
                                                       @if(Configuration::get('pick_from_bulk', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="pick_from_bulk">{{__('Pick directly from bulk locations')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Disables replenishment to pick locations')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="skip_staging" value="0"/>
                                                <input type="checkbox" class="custom-control-input" name="skip_staging"
                                                       id="skip_staging" value="1"
                                                       @if(Configuration::get('skip_staging', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="skip_staging">{{__('Skip staging location')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Pick directly to outbound dock(s)')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="manual_putaway" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="manual_putaway" id="manual_putaway" value="1"
                                                       @if(Configuration::get('manual_putaway', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="manual_putaway">{{__('Manual putaway')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Disables automatic location allocation during putaway')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="manual_order_no" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="manual_order_no" id="manual_order_no" value="1"
                                                       @if(Configuration::get('manual_order_no', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="manual_order_no">{{__('Manual order numbers')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Disables automatic order numbers')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="consolidate_outbound_crd" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="consolidate_outbound_crd" id="consolidate_outbound_crd"
                                                       value="1"
                                                       @if(Configuration::get('consolidate_outbound_crd', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="consolidate_outbound_crd">{{__('Consolidate outbound crossdock orders')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Automatically consolidates outbound crossdock orders if destinations match')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="fefo_picking" value="0"/>
                                                <input type="checkbox" class="custom-control-input" name="fefo_picking"
                                                       id="fefo_picking" value="1"
                                                       @if(Configuration::get('fefo_picking', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="fefo_picking">{{__('FEFO picking')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Pick using FEFO (first expiry out) strategy when possible')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="settings_tab_2" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="auto_start_order" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="auto_start_order"
                                                       id="auto_start_order" value="1"
                                                       @if(Configuration::get('auto_start_order', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="auto_start_order">{{__('Start order automatically')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Automatically start new orders')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="auto_start_replenishment" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="auto_start_replenishment"
                                                       id="auto_start_replenishment" value="1"
                                                       @if(Configuration::get('auto_start_replenishment', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="auto_start_replenishment">{{__('Start replenishment automatically')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Automatically start replenishment when possible')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="auto_start_picking" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="auto_start_picking"
                                                       id="auto_start_picking" value="1"
                                                       @if(Configuration::get('auto_start_picking', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="auto_start_picking">{{__('Start picking automatically')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Automatically start picking when possible')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="auto_start_shipping" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="auto_start_shipping"
                                                       id="auto_start_shipping" value="1"
                                                       @if(Configuration::get('auto_start_shipping', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="auto_start_shipping">{{__('Start shipping automatically')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Automatically start shipping when possible')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="settings_tab_3" role="tabpanel">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            Stock group types
                                                        </h3>
                                                        <div class="card-tools">
                                                            <div class="btn-group">
                                                                <button type="button"
                                                                        class="btn btn-block bg-gradient-primary"
                                                                        data-toggle="modal"
                                                                        data-target="#modal_new_stock_group_type">
                                                                    <i class="fas fa-plus"></i> {{__('New group type')}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <table id="stockGroupTypeTable"
                                                               class="table dataTable dtr-inline">
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="settings_tab_4" role="tabpanel">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="invoicing" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="invoicing"
                                                       id="invoicing" value="1"
                                                       @if(Configuration::get('invoicing', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="invoicing">{{__('Enable invoicing')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Enable / disable invoicing module')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Sales invoice:</h3>

                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="invoice_sales" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="invoice_sales"
                                                       id="invoice_sales" value="1"
                                                       @if(Configuration::get('invoice_sales', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="invoice_sales">{{__('Sales invoice')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Enable / disable sales invoicing')}}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <h3>Storage invoice:</h3>

                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="invoice_storage" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="invoice_storage"
                                                       id="invoice_storage" value="1"
                                                       @if(Configuration::get('invoice_storage', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="invoice_storage">{{__('Storage invoice')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Enable / disable storage fee invoicing')}}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label
                                                class="col-form-label">{{__('Storage invoice period')}}</label>
                                            <select class="form-control" name="invoice_storage_period">
                                                <option value="days"
                                                        @if(Configuration::get('invoice_storage_period', 'day') === 'day') selected @endif >
                                                    Days
                                                </option>
                                                <option value="weeks"
                                                        @if(Configuration::get('invoice_storage_period', 'day') === 'week') selected @endif>
                                                    Weeks
                                                </option>
                                                <option value="months"
                                                        @if(Configuration::get('invoice_storage_period', 'day') === 'month') selected @endif>
                                                    Months
                                                </option>
                                            </select>
                                            <span
                                                class="text-muted form-text">{{__('Set the period per which the storage should be invoiced')}}</span>
                                        </div>
                                    </div>


                                </div>
                                <div class="tab-pane" id="settings_tab_5" role="tabpanel">
                                    <h3>Label size:</h3>
                                    <div class="form-group row">
                                        <label
                                            class="col-lg-6 col-form-label">{{__('Width')}}</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control"
                                                   placeholder="Label width"
                                                   autocomplete="off" name="label_width"
                                                   value="{{Configuration::get('label_width', '')}}"
                                                   required="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label
                                            class="col-lg-6 col-form-label">{{__('Height')}}</label>
                                        <div class="col-lg-6">
                                            <input type="text" class="form-control"
                                                   placeholder="Label height"
                                                   autocomplete="off" name="label_height"
                                                   value="{{Configuration::get('label_height', '')}}"
                                                   required="">
                                        </div>
                                    </div>
                                    <h4>Zebra label printing:</h4>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <div class="custom-control custom-switch">
                                                <input type="hidden" name="zebra_printing" value="0"/>
                                                <input type="checkbox" class="custom-control-input"
                                                       name="zebra_printing"
                                                       id="zebra_printing" value="1"
                                                       @if(Configuration::get('zebra_printing', false)) checked @endif>
                                                <label class="custom-control-label"
                                                       for="zebra_printing">{{__('Enable Zebra printing')}}</label>
                                                <span
                                                    class="text-muted form-text">{{__('Enable / disable support for Zebra printing from scanner (through BrowserPrint)')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label
                                            class="col-lg-6 col-form-label">{{__('Zebra stock label')}}</label>
                                        <div class="col-lg-6">
                                        <textarea id="stock_label_template" class="form-control mb-1" rows="10"
                                                  placeholder="Zebra stock label template (ZPL)"
                                                  name="stock_label_template">{{Configuration::get('stock_label_template', '')}}</textarea>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm btn-inputtext mb-1"
                                                    data-targettextarea="#stock_label_template"
                                                    data-inputtext="{product:name}">Product:name
                                            </button>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm btn-inputtext mb-1"
                                                    data-targettextarea="#stock_label_template"
                                                    data-inputtext="{product:barcode}">Product:barcode
                                            </button>
                                            @foreach($stockgrouptypes as $stockgrouptype)
                                                <button type="button"
                                                        class="btn btn-primary btn-sm btn-inputtext mb-1"
                                                        data-targettextarea="#stock_label_template"
                                                        data-inputtext="{{!! $stockgrouptype->id !!}}">{{$stockgrouptype->id_name}}</button>
                                                @if($stockgrouptype->expires)
                                                    <button type="button"
                                                            class="btn btn-primary btn-sm btn-inputtext mb-1"
                                                            data-targettextarea="#stock_label_template"
                                                            data-inputtext="{{!! $stockgrouptype->id . ':expiry_date' !!}}">{{$stockgrouptype->id_name . ':expiry_date'}}</button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn bg-gradient-primary btn-modal-form-submit"
                                            data-target="#configurationForm">Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!--end::form-->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_stock_group_type" tabindex="-1" role="dialog"
         aria-labelledby="modalNewStockGroupTypeLabel"
         style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewStockGroupTypeLabel">{{ __('New group type') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newStockGroupTypeForm"
                          action="{{ url('/') }}/stockgrouptype" method="post">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the stock group type details below') }}
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
                                           for="name">{{ __('Enabled') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="enabled"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="enabled"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Required') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="required"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="required"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Physical') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="physical"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="physical"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Expires') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="expires"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="expires"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Specifiable') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="specify"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="specify"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('ID name') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('ID name') }}" autocomplete="off" name="id_name"
                                               value="" required>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Prefix') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('Prefix') }}" autocomplete="off" name="prefix"
                                               value="" required>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Label (single)') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('Label (single)') }}" autocomplete="off"
                                               name="label_single"
                                               value="" required>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Label (plural)') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('Label (plural)') }}" autocomplete="off"
                                               name="label_plural"
                                               value="" required>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="final_location_type_id">{{__('Final location type')}}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <select class="form-control selectpicker"
                                                name="final_location_type_id">
                                            <option value=""></option>
                                            @foreach( $locationtypes as $locationType)
                                                <option
                                                    value="{{$locationType->id}}">{{$locationType->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit"
                            data-target="#newStockGroupTypeForm">
                        {{ __('Save group type') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_stock_group_type" tabindex="-1" role="dialog"
         aria-labelledby="modalNewStockGroupTypeLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditStockGroupTypeLabel">{{ __('Edit stock group type') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editStockGroupTypeForm"
                          action="{{ url('/') }}/stockgrouptype" method="put">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the stock group type details below') }}
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
                                           for="name">{{ __('Enabled') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="enabled"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="enabled"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Required') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="required"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="required"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Physical') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="physical"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="physical"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Expires') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="expires"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="expires"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Specifiable') }}</label>
                                    <div class="col-9 pt-2">
                                        <div class="custom-control custom-switch">
                                            <input type="hidden"
                                                   name="specify"
                                                   value="0"/>
                                            <input type="checkbox"
                                                   class="custom-control-input"
                                                   name="specify"
                                                   value="1">
                                            <label class="custom-control-label"></label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('ID name') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('ID name') }}" autocomplete="off" name="id_name"
                                               value="" required>

                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Prefix') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('Prefix') }}" autocomplete="off" name="prefix"
                                               value="" required>

                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Label (single)') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('Label (single)') }}" autocomplete="off"
                                               name="label_single"
                                               value="" required>

                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="name">{{ __('Label (plural)') }}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('Label (plural)') }}" autocomplete="off"
                                               name="label_plural"
                                               value="" required>

                                    </div>
                                </div>
                                <div
                                    class="form-group row">
                                    <label class="col-form-label col-lg-3"
                                           for="final_location_type_id">{{__('Final location type')}}</label>
                                    <div class="col-lg-9 col-md-9">
                                        <select class="form-control selectpicker"
                                                name="final_location_type_id">
                                            <option value=""></option>
                                            @foreach( $locationtypes as $locationType)
                                                <option
                                                    value="{{$locationType->id}}">{{$locationType->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit"
                            data-target="#editStockGroupTypeForm">
                        {{ __('Save group type') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
            });

            function renderBool(value) {
                if (value === 1 || value === true) {
                    return "<i class='fas fa-check text-success'></i>"
                } else {
                    return "<i class='fas fa-times text-danger'></i>"
                }
            }

            $stockgrouptype_table = $('#stockGroupTypeTable').DataTable({
                processing: true,
                serverSide: false,
                searchDelay: 1000,
                responsive: true,
                ajax: {
                    url: '{{ url('/') }}/datatables/stockgrouptypes',
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
                    },
                    {
                        data: 'description',
                        name: 'description',
                        title: 'Description',
                    },
                    {
                        data: 'enabled',
                        name: 'enabled',
                        title: 'Enabled',
                        render: function (data, type, row, meta) {
                            return renderBool(data);
                        },
                    },
                    {
                        data: 'required',
                        name: 'required',
                        title: 'Required',
                        render: function (data, type, row, meta) {
                            return renderBool(data);
                        },
                    },
                    {
                        data: 'physical',
                        name: 'physical',
                        title: 'Physical',
                        render: function (data, type, row, meta) {
                            return renderBool(data);
                        },
                    },
                    {
                        data: 'expires',
                        name: 'expires',
                        title: 'Expires',
                        render: function (data, type, row, meta) {
                            return renderBool(data);
                        },
                    },
                    {
                        data: 'specify',
                        name: 'specify',
                        title: 'Specifiable',
                        render: function (data, type, row, meta) {
                            return renderBool(data);
                        },
                    },
                    {
                        data: 'id_name',
                        name: 'id_name',
                        title: 'ID naming',
                    },
                    {
                        data: 'prefix',
                        name: 'prefix',
                        title: 'Prefix',
                    },
                    {
                        data: 'label_single',
                        name: 'label_single',
                        title: 'Single',
                    },
                    {
                        data: 'label_plural',
                        name: 'label_plural',
                        title: 'Plural',
                    },
                    {
                        data: 'finallocationtype.name',
                        name: 'finallocationtype.name',
                        title: 'Final location type',
                    },
                    {
                        data: '',
                        name: 'actions',
                        title: 'Actions',
                        render: function (data, type, row, meta) {
                            return '<a href="#" data-target="' + row.id + '" class="editGroupType btn btn-xs btn-default btn-table" title="Edit"><i class="fa fa-edit"></i></a>' +
                                '<a href="#" data-target="' + row.id + '" class="deleteGroupType btn btn-xs btn-danger btn-table" title="Delete"><i class="far fa-trash-alt"></i></a>';
                        }
                    },
                ],
                autoWidth: false
            });
            $stockgrouptype_table.on('click', 'tr td a.editGroupType', function (e) {
                e.preventDefault();
                let id = $(this).data('target');

                $.ajax({
                    url: "{{ url('/stockgrouptype' ) }}/" + id,
                    headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    success: function (data) {
                        $('#modal_edit_stock_group_type form').attr('action', '{{ url('/') }}/stockgrouptype/' + id);
                        $('#modal_edit_stock_group_type form input:not([type=hidden])').each(function (index, element) {
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
                        $('#modal_edit_stock_group_type form select').each(function (index, element) {
                            const name = $(element).attr('name');
                            if (typeof (data[name]) != 'undefined') {
                                $(element).val(data[name]);
                                // $(element).selectpicker('refresh');
                            }
                        });
                        $('#modal_edit_stock_group_type').modal('show');
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
            $stockgrouptype_table.on('click', 'tr td a.deleteGroupType', function (e) {
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
                            url: "{{ url('/stockgrouptype' ) }}/" + id,
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
                                $stockgrouptype_table.ajax.reload();
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
        });
    </script>
@endpush
