@extends('layouts.scanner')

@section('title', 'Move finished')

@section('icon', 'fas fa-people-carry')

@section('back-link', url('/scanner/move' ))

@section('content')
    <div class="col-12">
        @if( $task )
            <a href="{{ url('/scanner/move/' . $task->id ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Do next move') }}
            </a>
            <a href="{{ url('/scanner/move/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Show move tasks') }}
            </a>
        @else
            <p>{{__( 'All move tasks are done!')}}</p>
        @endif
        <a href="{{ url('/scanner/' ) }}" class="btn btn-primary btn-lg btn-block">
            {{ __('Go to start screen') }}
        </a>
    </div>
@endsection
