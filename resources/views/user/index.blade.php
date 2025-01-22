@extends('layouts.default')

@section('title', 'Users')

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
                                    {{__('New user')}}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="userTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modal_new_user" tabindex="-1" role="dialog" aria-labelledby="modalNewUserLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNewOrderLabel">{{ __('Add User') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="newUserForm"
                          action="{{ url('/') }}/users">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the user details below') }}
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Username') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Username') }}" autocomplete="off" name="username"
                                           value="" required>
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
                                       for="email">{{ __('E-Mail Address') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('E-Mail Address') }}" autocomplete="off" name="email"
                                           value="" required>
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="password">{{ __('Password') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="password" class="form-control"
                                           placeholder="{{ __('Password') }}" autocomplete="off" name="password"
                                           value="" required>
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="password_confirmation">{{ __('Confirm Password') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="password" class="form-control"
                                           placeholder="{{ __('Password') }}" autocomplete="off"
                                           name="password_confirmation" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="userRole">{{ __('User Role') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            data-live-search="true"
                                            name="role_id">
                                        @foreach( $roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="userRole">{{ __('Contact') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            data-live-search="true"
                                            name="contact_id">
                                        <option value=""></option>
                                        @foreach( $contacts as $contact)
                                            <option value="{{$contact->id}}">{{$contact->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#newUserForm">
                        {{ __('Add user') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_edit_user" tabindex="-1" role="dialog" aria-labelledby="modalEditUserLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditUserLabel">{{ __('Edit User') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="" id="editUserForm" action="{{ url('/') }}/users" method="put">
                        <div class="container-fluid">
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the user details below') }}
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="name">{{ __('Username') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('Username') }}" autocomplete="off" name="username"
                                           value="" required>
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
                                       for="email">{{ __('E-Mail Address') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="text" class="form-control"
                                           placeholder="{{ __('E-Mail Address') }}" autocomplete="off" name="email"
                                           value="" required>
                                </div>
                            </div>
                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="password">{{ __('Password') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="password" class="form-control"
                                           placeholder="{{ __('Password') }}" autocomplete="off" name="password"
                                           value="" required>
                                </div>
                            </div>

                            <div
                                class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="password_confirmation">{{ __('Confirm Password') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <input type="password" class="form-control"
                                           placeholder="{{ __('Password') }}" autocomplete="off"
                                           name="password_confirmation" value="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="userRole">{{ __('User Role') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            data-live-search="true"
                                            name="role_id">
                                        @foreach( $roles as $role)
                                            <option value="{{$role->id}}">{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="userRole">{{ __('Contact') }}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            data-live-search="true"
                                            name="contact_id">
                                        <option value=""></option>
                                        @foreach( $contacts as $contact)
                                            <option value="{{$contact->id}}">{{$contact->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#editUserForm">
                        {{ __('Update user') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('modals')
    <div class="modal fade" id="modal_api_token" tabindex="-1" role="dialog" aria-labelledby="ApiTokenModalLabel"
         aria-modal="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ApiTokenModalLabel">New API token</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="alert alert-primary" role="alert">
                        {{ __('This is your new API token, store this somewhere safe!') }}
                    </div>
                    <p class="api_token_text"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $table = $('#userTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/users',
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
                    data: 'username',
                    name: 'username',
                    title: 'Username',
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Name',
                },
                {
                    data: 'email',
                    name: 'email',
                    title: 'E-mail',
                },
                {
                    data: 'roles',
                    name: 'roles',
                    title: 'Role',
                    render: function (data, type, row, meta) {
                        return row.roles[0].name;
                    }
                },
                {
                    data: 'contact.name',
                    name: 'contact.name',
                    title: 'Contact',
                },
                {
                    title: 'Actions',
                    name: 'actions',
                    width: '150px',
                    render: function (data, type, row, meta) {
                        return '' +
                            '<a href="#" data-target="' + row.id + '" class="editUser btn btn-xs btn-default btn-table" title="Delete"><i class="far fa-edit"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="generateQr btn btn-xs btn-default btn-table" title="Generate new QR code"><i class="fa fa-qrcode"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="generateApiKey btn btn-xs btn-default btn-table" title="Generate new API token"><i class="fa fa-key"></i></a>' +
                            '<a href="#" data-target="' + row.id + '" class="deleteUser btn btn-xs btn-danger btn-table" title="Delete"><i class="far fa-trash-alt"></i></a>' +
                            '';
                    },
                    sortable: false
                },
            ],
            autoWidth: false
        });

        $table.on('click', 'tr td a.deleteUser', function (e) {
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
                        url: "{{ url('/users/delete/' ) }}",
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

        $table.on('click', 'tr td a.generateQr', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            window.open("{{ url('/') }}/users/generateqr/" + id);
        });

        $table.on('click', 'tr td a.generateApiKey', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            $.ajax({
                url: "{{ url('/users/generateapitoken/' ) }}",
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'POST',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    if (data.success) {
                        // Success
                        toastr.success(data.message, 'Success');
                        $('.api_token_text').html('<strong>' + data.api_token + '</strong>');
                        $('#modal_api_token').modal('show');
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
        });

        $table.on('click', 'tr td a.editUser', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/users' ) }}/" + id,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#modal_edit_user form').attr('action', '{{ url('/') }}/users/' + id);
                    $('#modal_edit_user form input').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (typeof (data[name]) != 'undefined') {
                            $(element).val(data[name]);
                        }
                    });
                    $('#modal_edit_user form select').each(function (index, element) {
                        const name = $(element).attr('name');
                        if (name === 'role_id') {
                            if (typeof (data['roles'][0]) != 'undefined') {
                                $(element).val((data['roles'][0].id));
                                // $(element).selectpicker('refresh');
                            }
                        } else {
                            if (typeof (data[name]) != 'undefined') {
                                $(element).val(data[name]);
                                // $(element).selectpicker('refresh');
                            }
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
