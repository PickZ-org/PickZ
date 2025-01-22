@extends('layouts.scanner')

@section('title', 'Crossdock task finished')

@section('icon', 'fas fa-arrows-alt-h')

@section('back-link', url('/scanner/crossdock'))

@section('content')
    <div class="col-12">
        @if( $task )
            <a href="{{ url('/scanner/crossdock/' . $task->id ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Do next crossdock job') }}
            </a>
            <a href="{{ url('/scanner/crossdock/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Show crossdock tasks') }}
            </a>
        @else
            <p>{{__( 'All crossdock tasks are done!')}}</p>
        @endif
        <a href="{{ url('/scanner/' ) }}" class="btn btn-primary btn-lg btn-block">
            {{ __('Go to start screen') }}
        </a>
    </div>
@endsection
