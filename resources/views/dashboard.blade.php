@extends('layouts.default')

@section('title', 'Welcome, ' . Auth::user()->name )

@if(Auth::user()->hasRole(['admin', 'manager']))
    @section('content')

        <div class="container-fluid">
            <div class="row">
                <!-- Inbound Orders Widget -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0 bg-info text-white">
                            <h3 class="card-title">Inbound Orders</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="inboundChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Outbound Orders Widget -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0 bg-success text-white">
                            <h3 class="card-title">Outbound Orders</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="outboundChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Tasks Widget -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header border-0 bg-danger text-white">
                            <h3 class="card-title">Tasks</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="tasksChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const ctxInbound = document.getElementById('inboundChart').getContext('2d');
                new Chart(ctxInbound, {
                    type: 'doughnut',
                    data: {
                        labels: [@forEach($orderCount['inbound'] as $order) '{{ $order->count }} {{ $order->status->name }}', @endforeach],
                        datasets: [{
                            data: [@forEach($orderCount['inbound'] as $order) {{ $order->count }}, @endforeach],
                            backgroundColor: ['#17a2b8', '#28a745']
                        }]
                    }
                });

                const ctxOutbound = document.getElementById('outboundChart').getContext('2d');
                new Chart(ctxOutbound, {
                    type: 'doughnut',
                    data: {
                        labels: [@forEach($orderCount['outbound'] as $order) '{{ $order->count }} {{ $order->status->name }}', @endforeach],
                        datasets: [{
                            data: [@forEach($orderCount['outbound'] as $order) {{ $order->count }}, @endforeach],
                            backgroundColor: ['#17a2b8', '#ffc107', '#28a745']
                        }]
                    }
                });

                const ctxTasks = document.getElementById('tasksChart').getContext('2d');
                new Chart(ctxTasks, {
                    type: 'doughnut',
                    data: {
                        labels: ['{{ $taskCount['putaway'] }} Putaway', '{{ $taskCount['replenish'] }} Replenish', '{{ $taskCount['pick'] }} Picking'],
                        datasets: [{
                            data: [{{ $taskCount['putaway'] }}, {{ $taskCount['replenish'] }}, {{ $taskCount['pick'] }}],
                            backgroundColor: ['#6c757d', '#007bff', '#ffc107'],
                        }]
                    }
                });
            });
        </script>
    @endpush
@endif
