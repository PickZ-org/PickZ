@extends('layouts.default')

@section('title', 'Welcome, ' . Auth::user()->name )

@if(Auth::user()->hasRole(['admin', 'manager']))
    @section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">
                    <div class="small-box bg-info" style="min-height:calc(100% - 20px);">
                        <div class="inner">
                            <h3> {{ $orderCount['totalInbound'] }} </h3>
                            <p>{{ __('Inbound orders') }}</p>
                            <div class="d-flex justify-content-between">
                                @foreach($orderCount['inbound'] as $order)
                                    <span class="badge text-white p-2"
                                          style="font-size:16px;background-color:{{ $order->status->color }};">
                                        {{ $order->count }}
                                        <br/> {{ \Illuminate\Support\Str::limit($order->status->name, '10', '..')}}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-download"></i>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="small-box bg-success" style="min-height:calc(100% - 20px);">
                        <div class="inner">
                            <h3> {{ $orderCount['totalOutbound'] }} </h3>
                            <p>{{ __('Outbound orders') }}</p>
                            <div class="d-flex justify-content-between">
                                @foreach($orderCount['outbound'] as $order)
                                    <span class="badge text-white p-2"
                                          style="font-size:16px;background-color:{{ $order->status->color }};">
                                        {{ $order->count }}
                                        <br/> {{ \Illuminate\Support\Str::limit($order->status->name, '10', '..')}}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-upload"></i>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="small-box bg-danger" style="min-height:calc(100% - 20px);">
                        <div class="inner">
                            <h3> {{ $taskTotal }} </h3>
                            <p>{{ __('Tasks') }}</p>
                            <div class="d-flex justify-content-between">
                                <span class="badge text-white p-2"
                                      style="font-size:16px;background-color:{{ $taskColors['putaway'] }};">
                                        {{ $taskCount['putaway'] }}
                                        <br/> {{ __('Putaway') }}
                                    </span>
                                <span class="badge text-white p-2"
                                      style="font-size:16px;background-color:{{ $taskColors['replenish'] }};">
                                        {{ $taskCount['replenish'] }}
                                        <br/> {{ __('Replenish') }}
                                    </span>
                                <span class="badge text-white p-2"
                                      style="font-size:16px;background-color:{{ $taskColors['pick'] }};">
                                        {{ $taskCount['pick'] }}
                                        <br/> {{ __('Picking') }}
                                    </span>
                            </div>
                        </div>
                        <div class="icon">
                            <i class="ion ion-md-checkbox-outline"></i>
                        </div>
                    </div>
                </div>
            </div>
            @if($upcomingOrders->count())
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header border-transparent">
                                <h3 class="card-title">{{ __('Upcoming orders') }}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Order') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Delivery date') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($upcomingOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ url('/') }}/orders/{{ $order->id }}">{{ $order->order_no }}</a>
                                                </td>
                                                <td>
                                                    {{ $order->type->name }}
                                                </td>
                                                <td>
                                                <span class="badge"
                                                      style="background-color:{{ $order->status->color }};color:#ffffff;">{{ $order->status->name }}</span>
                                                </td>
                                                <td>
                                                    {{ $order->req_delivery_date }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endsection

    @push('scripts')
        <script>
        </script>
    @endpush
@endif
