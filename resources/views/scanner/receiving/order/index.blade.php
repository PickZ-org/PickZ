@extends('layouts.scanner')

@section('title', 'Receive order')

@section('icon', 'fas fa-dolly')

@section('back-link', url('/scanner/receiving'))

@section('content')
    <div class="col-12">
        @if( ! $orders->isEmpty())
            <div class="list-group">
                @foreach ( $orders as $order)
                    <a href="{{ url('/scanner/receiving/order/' . $order->id) }}"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{$order->order_no}}</h5>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <div>
                                {{$order->contact->name}}
                            </div>
                            <div>
                                Lines: {{$order->orderlines->count()}}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="row bg-white shadow-sm">
                <div class="col-12 p-2 text-center">
                    No new orders
                </div>
                <div class="col-12 p-2">
                    <a href="{{url('/scanner/receiving')}}" class="btn btn-primary btn-lg btn-block">
                        Go back
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
