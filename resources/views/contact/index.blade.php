@extends('layouts.default')

@section('title', 'Contacts')

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
                                        data-target="#modal_new_user">
                                    <i class="fa fa-user-plus"></i>
                                    {{__('New Contact')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="contactTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_user" tabindex="-1" role="dialog" aria-labelledby="modalNewContactLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewContactLabel">{{ __('New Contact') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newContactForm" action="{{ url('/') }}/contacts">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Contact details below') }}
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Name') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Name') }}" autocomplete="off" name="name"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="address1">{{ __('Address') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Address') }}" autocomplete="off" name="address1"
                                           value="">
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="address2">{{ __('') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Address line 2') }}" autocomplete="off"
                                           name="address2"
                                           value="">
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="address3">{{ __('') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Address line 3') }}" autocomplete="off"
                                           name="address3"
                                           value="">
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('Postal code') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Postal code') }}" autocomplete="off"
                                           name="postalcode"
                                           value=""
                                           required>

                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('City') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('City') }}" autocomplete="off" name="city"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('State') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('State') }}" autocomplete="off" name="state"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('Country') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Country') }}" autocomplete="off" name="country"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('Phone') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Phone') }}" autocomplete="off" name="phone"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('E-Mail Address') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('E-Mail Address') }}" autocomplete="off" name="email"
                                           value="" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#newContactForm">
                        {{ __('Save contact') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_user" tabindex="-1" role="dialog" aria-labelledby="modalEditContactLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditContactLabel">{{ __('Edit Contact') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editContactForm" action="{{ url('/') }}/contacts" method="put">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the Contact details below') }}
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Name') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Name') }}" autocomplete="off" name="name"
                                           value=""
                                           required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="address1">{{ __('Address') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Address') }}" autocomplete="off" name="address1"
                                           value="">
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="address2">{{ __('') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Address line 2') }}" autocomplete="off"
                                           name="address2"
                                           value="">
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="address3">{{ __('') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Address line 3') }}" autocomplete="off"
                                           name="address3"
                                           value="">
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('Postal code') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Postal code') }}" autocomplete="off"
                                           name="postalcode"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('City') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('City') }}" autocomplete="off" name="city"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('State') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('State') }}" autocomplete="off" name="state"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('Country') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Country') }}" autocomplete="off" name="country"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('Phone') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('Phone') }}" autocomplete="off" name="phone"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="email">{{ __('E-Mail Address') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control kt-input"
                                           placeholder="{{ __('E-Mail Address') }}" autocomplete="off" name="email"
                                           value="" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#editContactForm">
                        {{ __('Update contact') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush


@push('scripts')
    <script>
        $table = $('#contactTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/contacts',
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
                    data: 'address1',
                    name: 'address1',
                    title: 'Adress',
                    render: function (data, type, row, meta) {
                        let address = row.address1;
                        if (row.address2) {
                            address += ', ' + row.address2;
                        }
                        if (row.address3) {
                            address += ', ' + row.address3;
                        }
                        return address;
                    }
                },
                {
                    data: 'postalcode',
                    name: 'postalcode',
                    title: 'Postal code',
                },
                {
                    data: 'city',
                    name: 'city',
                    title: 'City',
                },
                {
                    data: 'state',
                    name: 'state',
                    title: 'State',
                },
                {
                    data: 'country',
                    name: 'country',
                    title: 'Country',
                },
                {
                    data: 'email',
                    name: 'email',
                    title: 'E-mail',
                },
                {
                    data: 'phone',
                    name: 'phone',
                    title: 'Phone',
                },
                {
                    title: 'Actions',
                    name: 'actions',
                    width: '75px',
                    render: function (data, type, row, meta) {
                        return '<a href="#" data-target="' + row.id + '" class="editContact btn btn-default btn-xs btn-table" title="Edit"><i class="far fa-edit"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="deleteContact btn btn-danger btn-xs btn-table" title="Delete"><i class="far fa-trash-alt"></i></a>';
                    },
                    sortable: false
                },
            ],
            autoWidth:false
        });

        $table.on('click', 'tr td a.deleteContact', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/contacts' ) }}/" + id,
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

        });

        $table.on('click', 'tr td a.editContact', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/contacts' ) }}/" + id,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#modal_edit_user form').attr('action', '{{ url('/') }}/contacts/' + id);
                    $('#modal_edit_user form input').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                        }
                    });
                    $('#modal_edit_user').modal('show');
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

    </script>
@endpush
