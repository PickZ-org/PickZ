@extends('layouts.scanner')

@section('title', 'Pick task finished')

@section('icon', 'fas fa-hands')

@section('back-link', url('/scanner/picking'))

@section('content')
    <div class="col-12">
        @if( $task )
            <a href="{{ url('/scanner/picking/' . $task->id ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Do next pick job') }}
            </a>
            <a href="{{ url('/scanner/picking/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Show picking tasks') }}
            </a>
        @else
            <p>{{__( 'All picking tasks are done!')}}</p>
        @endif
        <a href="{{ url('/scanner/' ) }}" class="btn btn-primary btn-lg btn-block">
            {{ __('Go to start screen') }}
        </a>
    </div>
@endsection
