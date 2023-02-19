@extends('layouts.scanner')

@section('title', 'Putaway finished')

@section('icon', 'fa fa-dolly-flatbed ')

@section('back-link', url('/scanner/putaway/' ))

@section('content')
    <div class="col-12">
        @if( $task )
            <a href="{{ url('/scanner/putaway/' . $task->id ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Do next putaway') }}
            </a>
            <a href="{{ url('/scanner/putaway/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Show putaway tasks') }}
            </a>
        @else
            <p>{{__( 'All putaway tasks are done!')}}</p>
        @endif
            <a href="{{ url('/scanner/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Go to start screen') }}
            </a>
    </div>
@endsection
