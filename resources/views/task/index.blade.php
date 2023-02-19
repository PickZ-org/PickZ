@extends('layouts.default')

@section('title', 'Tasks')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                        </h3>
                    </div>
                    <div class="card-body">
                        <table id="taskTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const $table = $('#taskTable').DataTable({
            processing: true,
            serverSide: false,
            searchDelay: 1000,
            ajax: {
                url: '{{ url('/') }}/datatables/tasks',
                type: 'POST',
                beforeSend: function (request) {
                    request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                },
                data: {
                    type: '{{is_array($type) ? implode(',', $type) : $type}}'
                }
            },
            columnDefs: [{
                defaultContent: "",
                targets: "_all"
            }],
            columns: [
                {
                    data: null,
                    defaultContent: '<i class="fas fa-plus-circle"></i>',
                    className: 'details-control',
                    orderable: false,
                },
                {
                    data: 'name',
                    name: 'name',
                    title: 'Task',
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Created',
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                    title: 'User',
                },
            ]
        });

        function subTable(data) {
            var div = $('<div/>').addClass('loading').text('Loading...');
            $.ajax({
                url: '{{ url('/') }}/datatables/tasklines',
                method: 'POST',
                headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                data: {
                    task_id: data.id
                },
                dataType: 'json',
                success: function (json) {
                    let subTable = $('<table/>').addClass('table').addClass('table-sm');
                    let subTableHeaders = $('<thead/>')
                        .append($('<th/>').html('Order'))
                        .append($('<th/>').html('Priority'))
                        .append($('<th/>').html('From'))
                        .append($('<th/>').html('To'))
                        .append($('<th/>').html('SKU'))
                        .append($('<th/>').html('UOM'))
                        .append($('<th/>').html('Quantity'))
                        .append($('<th/>').html('Actions'));
                    let subTableBody = $('<tbody/>');
                    $.each(json.data, function (key, item) {
                        let row = $('<tr/>')
                            .append($('<td/>').html('<a class="" href="{{ url('/') }}/orders/' + item.order.id + '">' + item.order.order_no + '</a>'))
                            .append($('<td/>').html(item.priority))
                            .append($('<td/>').html(item.stock.location.name))
                            .append($('<td/>').html(item.destination.name))
                            .append($('<td/>').html(item.stock.product.sku))
                            .append($('<td/>').html(item.stock.productuom.name))
                            .append($('<td/>').html(item.quantity))
                            .append($('<td/>').html('<a href="#" data-target="' + item.id + '" class="completeTaskLine btn btn-default btn-xs btn-table" title="Complete"><i class="fas fa-check-circle"></i></a>'));
                        subTableBody.append(row);
                    });
                    subTable.append(subTableHeaders).append(subTableBody);
                    div.html(subTable).removeClass('loading');
                }
            });

            return div;
        }

        $table.on('click', 'td.details-control', function () {
            let tr = $(this).closest('tr');
            let row = $table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                $(this).html('<i class="fas fa-plus-circle"></i>');
                tr.removeClass('shown');
            } else {
                row.child(subTable(row.data()), 'child-row').show();
                $(this).html('<i class="fas fa-minus-circle"></i>');
                tr.addClass('shown');
            }
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

                    // Reload subtable
                    let tr = $('#taskTable').find('tr.shown');
                    let row = $table.row(tr);
                    if (row.child.isShown()) {
                        row.child(subTable(row.data()), 'child-row').show();
                    }
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
