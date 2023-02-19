@extends('layouts.default')

@section('title', 'Locations')

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
                                        data-target="#modal_new_location">
                                    <i class="fas fa-plus"></i> {{__('New location')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="locationTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_location" tabindex="-1" role="dialog"
         aria-labelledby="modalNewLocationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewLocationLabel">{{ __('New Location') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newLocationForm"
                          action="{{ url('/') }}/locations">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Location details below') }}
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
                                       for="name">{{ __('Barcode') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Barcode') }}" autocomplete="off" name="barcode"
                                           value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="locationType">{{__('Location type')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            name="location_type_id">
                                        @foreach( $locationTypes as $type)
                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#newLocationForm">
                        {{ __('Save location') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_location" tabindex="-1" role="dialog"
         aria-labelledby="modalEditLocationLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLocationLabel">{{ __('Edit Location') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editLocationForm"
                          action="{{ url('/') }}/locations" method="put">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Location details below') }}
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
                                       for="name">{{ __('Barcode') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Barcode') }}" autocomplete="off" name="barcode"
                                           value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="locationType">{{__('Location type')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            name="location_type_id">
                                        @foreach( $locationTypes as $type)
                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Task order') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="number" min="0" step="1" class="form-control"
                                           placeholder="{{ __('Task order') }}" autocomplete="off"
                                           name="location_order"
                                           value="" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#editLocationForm">
                        {{ __('Update location') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $table = $('#locationTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/locations',
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
                    data: 'barcode',
                    name: 'barcode',
                    title: 'Barcode',
                },
                {
                    data: 'location_order',
                    name: 'location_order',
                    title: 'Tasks order',
                },
                {
                    title: 'Actions',
                    name: 'actions',
                    width: '150px',
                    render: function (data, type, row, meta) {
                        return '' +
                            '<a href="#" data-target="' + row.id + '" class="editLocation btn btn-xs btn-default btn-table" title="Delete"><i class="far fa-edit"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="locationLabel btn btn-xs btn-default btn-table" title="Generate new QR code"><i class="fas fa-barcode"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="deleteLocation btn btn-xs btn-danger btn-table" title="Delete"><i class="far fa-trash-alt"></i></a>' +
                            '';
                    },
                    sortable: false
                },
            ],
            autoWidth: false
        });

        $table.on('click', 'tr td a.deleteLocation', function (e) {
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
                        url: "{{ url('/locations' ) }}/" + id,
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
                            $table.ajax.reload();
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
            });
        });

        $table.on('click', 'tr td a.editLocation', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/locations' ) }}/" + id,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#modal_edit_location form').attr('action', '{{ url('/') }}/locations/' + id);
                    $('#modal_edit_location form input').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                        }
                    });
                    $('#modal_edit_location form select').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                            // $(element).selectpicker('refresh');
                        }
                    });
                    $('#modal_edit_location').modal('show');
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

        $table.on('click', 'tr td a.locationLabel', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            window.open("{{ url('/') }}/document/locationlabel/" + id);
        });
    </script>
@endpush
