@extends('layouts.default')

@section('title', 'Logs')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="logTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $table = $('#logTable').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 1000,
            responsive: true,
            ajax: {
                url: '{{ url('/') }}/datatables/logs',
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
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Date',
                },
                {
                    data: 'description',
                    name: 'description',
                    title: 'Description',
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                    title: 'User',
                },
                {
                    data: 'order.order_no',
                    name: 'order.order_no',
                    title: 'Order',
                },
                {
                    data: 'tasks.name',
                    name: 'tasks.name',
                    title: 'Tasks',
                },
                {
                    data: 'product.name',
                    name: 'product.name',
                    title: 'Product',
                },
                {
                    data: 'location.name',
                    name: 'location.name',
                    title: 'Location',
                },
            ],
            autoWidth: false
        });
    </script>
@endpush
