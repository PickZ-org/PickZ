@extends('layouts.default')

@section('title', 'Tasks')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-body">
                        <table id="tasklineTable" class="table table-striped dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="destination_modal" tabindex="-1" role="dialog"
         aria-labelledby="setDestinationModalLabel" aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="setDestinationModalLabel">Destination</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form class="" id="completeTaskForm"
                              action="{{ url('/tasklines') }}/" method="put">
                            <input type="hidden" name="action" value="complete"/>
                            <input type="hidden" name="id" value="" id="task_id"/>
                            <div class="form-group">
                                <div class="alert alert-primary" role="alert">
                                    {{ __('Fill in the location details below') }}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-lg-3"
                                       for="location_id">{{__('Destination')}}</label>
                                <div class="col-lg-9 col-md-9">
                                    <select class="form-control selectpicker"
                                            name="location_id">
                                        @foreach( $bulklocations as $location)
                                            <option value="{{$location->id}}">{{$location->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-modal-form-submit" data-target="#completeTaskForm">
                        {{ __('Complete task') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $table = $('#tasklineTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            ajax: {
                url: '{{ url('/') }}/datatables/tasklines',
                type: 'POST',
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                },
                data: {
                    type: '{{$type}}'
                }
            },
            columnDefs: [{
                defaultContent: "",
                targets: "_all"
            }],
            columns: [
                {
                    data: 'stock.location.name',
                    name: 'stock.location.name',
                    title: 'From',
                },
                {
                    data: 'destination.name',
                    name: 'destination.name',
                    title: 'To',
                },
                {
                    data: 'stock.product.name',
                    name: 'stock.product.name',
                    title: 'Product',
                },
                {
                    data: 'stock.product.sku',
                    name: 'stock.product.sku',
                    title: 'SKU',
                },
                {
                    data: 'stock.productuom.name',
                    name: 'stock.productuom.name',
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
                {
                    title: 'Actions',
                    name: 'actions',
                    render: function (data, type, row, meta) {
                        if (row.destination !== null) {
                            return '<a href="#" data-target="' + row.id + '" class="completeTaskLine btn btn-default btn-xs btn-table" title="Complete task"><i class="far fa-check-circle"></i></a>';
                        } else {
                            return '<a href="#" data-target="' + row.id + '" class="setDestinationModal btn btn-default btn-xs btn-table" title="Complete task"><i class="far fa-check-circle"></i></a>';
                        }
                    },
                    sortable: false
                },
            ]
        });

        $table.on('click', 'tr td a.completeTaskLine', function (e) {
            e.preventDefault();
            let id = $(this).data('target');

            $.ajax({
                url: "{{ url('/tasklines') }}/" + id,
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                type: 'PUT',
                dataType: 'JSON',
                data: {
                    action: 'complete',
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
            })

        });

        $table.on('click', 'tr td a.setDestinationModal', function (e) {
            e.preventDefault();
            let id = $(this).data('target');
            // Set correct action
            $('#completeTaskForm').attr('action', "{{ url('/tasklines') }}/" + id);
            // Set correct id
            $('#task_id').val(id);
            // Open form modal
            $('#destination_modal').modal('show');
        });
    </script>
@endpush
