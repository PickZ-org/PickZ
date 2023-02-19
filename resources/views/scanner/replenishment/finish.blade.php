@extends('layouts.scanner')

@section('title', 'Replenishment finished')

@section('icon', 'fa fa-sync-alt')

@section('back-link', url('/scanner/replenishment' ))

@section('content')
    <div class="col-12">
        @if( $task )
            <a href="{{ url('/scanner/replenishment/' . $task->id ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Do next replenishment') }}
            </a>
            <a href="{{ url('/scanner/replenishment/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Show replenishment tasks') }}
            </a>
        @else
            <p>{{__( 'All replenishment tasks are done!')}}</p>
        @endif
        <a href="{{ url('/scanner/' ) }}" class="btn btn-primary btn-lg btn-block">
            {{ __('Go to start screen') }}
        </a>
    </div>
@endsection
