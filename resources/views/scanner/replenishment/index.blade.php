@extends('layouts.scanner')

@section('title', 'Replenishment')

@section('icon', 'fa fa-sync-alt')

@section('back-link', url('/scanner'))

@section('content')
    <div class="col-12">
        @if( ! $tasks->isEmpty())
            <div class="list-group">
                @foreach ( $tasks as $task)
                    <a href="{{ url('/scanner/replenishment/' . $task->id) }}"
                       class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{$task->stock->product->name}}</h5>
                        </div>
                        <div class="d-flex w-100 justify-content-between">
                            <div>
                                Location: {{$task->stock->location->name}}
                            </div>
                            <div>
                                Destination: {{$task->destination->name}}
                            </div>
                            <div>
                                Quantity: {{$task->quantity}}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="row bg-white shadow-sm">
                <div class="col-12 p-2 text-center">
                    No replenishment tasks
                </div>
                <div class="col-12 p-2">
                    <a href="{{url('/scanner')}}" class="btn btn-primary btn-lg btn-block">
                        Go back
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
