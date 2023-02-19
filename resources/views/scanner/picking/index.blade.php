@extends('layouts.scanner')

@section('title', 'Picking')

@section('icon', 'fas fa-hands')

@section('back-link', url('/scanner'))

@section('content')
    <div class="col-12">
        @if( ! $tasks->isEmpty())
            <div class="list-group">
                @foreach ( $tasks as $task)
                    <a href="{{ url('/scanner/picking/' . $task->id) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{$task->name}}</h5>
                        </div>
                        <div class="w-100 justify-content-between">
                            <div class="d-inline">
                                Lines:
                                @if($task->type->id === 3 )
                                    {{$task->tasklines()->count()}}
                                @elseif($task->type->id === 7 )
                                    {{$task->tasklines()->groupBy('source_stock_id')->count()}}
                                @endif
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
                    No Picking tasks
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
