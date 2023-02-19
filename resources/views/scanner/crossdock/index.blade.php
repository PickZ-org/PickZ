@extends('layouts.scanner')

@section('title', 'Crossdock')

@section('icon', 'fas fa-arrows-alt-h')

@section('back-link', url('/scanner'))

@section('content')
    <div class="col-12">
        @if( ! $tasks->isEmpty())
            <div class="list-group">
                @foreach ( $tasks as $task)
                    <a href="{{ url('/scanner/crossdock/' . $task->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{$task->name}}</h5>
                            <small>{{$task->order->contact->name}}</small>
                        </div>
                        <div class="w-100 justify-content-between">
                            <div class="d-inline">
                                Lines: {{$task->tasklines()->count()}}
                            </div>
                            @if( $task->user_id)
                                <span class="badge badge-primary badge-pill float-right">Assigned to you</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="row bg-white shadow-sm">
                <div class="col-12 p-2 text-center">
                    No Crossdock tasks
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
