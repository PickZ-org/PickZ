@extends('layouts.default')

@section('title', 'Invoices')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table id="invoiceTable" class="table dataTable dtr-inline">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $datatable = $('#invoiceTable').DataTable({
                processing: true,
                serverSide: true,
                searchDelay: 1000,
                responsive: true,
                ajax: {
                    url: '{{ url('/') }}/datatables/invoices',
                    type: 'POST',
                    beforeSend: function (request) {
                        request.setRequestHeader('X-CSRF-TOKEN', window.Laravel.csrfToken);
                    }
                },
                columnDefs: [{
                    defaultContent: "",
                    targets: "_all"
                }],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false,
                    },
                    {
                        data: null,
                        defaultContent: '<i class="fas fa-plus-circle"></i>',
                        className: 'details-control',
                        orderable: false,
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no',
                        title: 'Invoice No',
                    },
                    {
                        data: 'status.name',
                        name: 'status.name',
                        title: 'Status',
                        render: function (data, type, row, meta) {
                            return '<span class="badge badge-pill" style="background-color:' + row.status.color + ';color:#ffffff;">' + row.status.name + '</span>';
                        }
                    },
                    {
                        data: 'type.name',
                        name: 'type.name',
                        title: 'Invoice type',
                    },
                    {
                        data: 'contact.name',
                        name: 'contact.name',
                        title: 'Contact',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        title: 'Created',
                    },
                ],
                order: [
                    [2, 'asc']
                ],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        text: 'Bulk actions',
                        split:
                            [
                                {
                                    text: '<i class="fa fa-file-export"></i> Export',
                                    action: function (e, dt) {
                                        bulkAction(dt.rows({selected: true}).data(), 'export', true);
                                    }
                                },
                                {
                                    text: '<i class="fa fa-file-export"></i> Export & close',
                                    action: function (e, dt) {
                                        bulkAction(dt.rows({selected: true}).data(), 'exportclose', true);
                                    }
                                },
                                {
                                    text: '<i class="fa fa-check-square"></i> Close',
                                    action: function (e, dt) {
                                        bulkAction(dt.rows({selected: true}).data(), 'close');
                                    }
                                },
                            ],
                    }
                ]
            });

            $datatable.on('click', 'td.details-control', function () {
                let tr = $(this).closest('tr');
                let row = $datatable.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    $(this).html('<i class="fas fa-plus-circle"></i>')
                } else {
                    row.child(subTable(row.data()), 'child-row').show();
                    $(this).html('<i class="fas fa-minus-circle"></i>')
                }
            });

            function subTable(data) {
                var div = $('<div/>').addClass('loading').text('Loading...');
                $.ajax({
                    url: '{{ url('/') }}/datatables/invoicelines',
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                    data: {
                        invoice_id: data.id
                    },
                    dataType: 'json',
                    success: function (json) {
                        let subTable = $('<table/>').addClass('table').addClass('table-sm');
                        let subTableHeaders = $('<thead/>')
                            .append($('<th/>').html('Description'))
                            .append($('<th/>').html('Quantity'))
                            .append($('<th/>').html('Price'));
                        let subTableBody = $('<tbody/>');
                        $.each(json.data, function (key, item) {
                            let row = $('<tr/>')
                                .append($('<td/>').html(item.description))
                                .append($('<td/>').html(item.quantity))
                                .append($('<td/>').html(item.price));
                            subTableBody.append(row);
                        });
                        subTable.append(subTableHeaders).append(subTableBody);
                        div.html(subTable).removeClass('loading');
                    }
                });

                return div;
            }

            function bulkAction(rows, action, noajax = false) {
                let ids = [];
                $(rows).each(function (index, value) {
                    ids.push(value.id);
                });
                if (noajax) {
                    let $form = $('<form />').attr('action', '{{ url('/invoices') }}/bulk').attr('method', 'post');
                    $form.append($('<input type="hidden" name="ids[]" />').val(ids));
                    $form.append($('<input type="hidden" name="action" />').val(action));
                    $form.append($('<input type="hidden" name="_token" />').val(window.Laravel.csrfToken));
                    $(document.body).append($form);
                    $form.submit();
                    $datatable.ajax.reload();
                } else {
                    $.ajax({
                        url: "{{ url('/invoices') }}/bulk",
                        headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken},
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            action: action,
                            ids: ids
                        },
                        success: function (data) {
                            if (data.success) {
                                // Success
                                toastr.success(data.message, 'Success');
                            } else {
                                // Show error info
                                toastr.error(data.message, 'Error');
                            }
                            $datatable.ajax.reload();
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
            }
        });
    </script>
@endpush
