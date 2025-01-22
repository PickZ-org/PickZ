@extends('layouts.scanner')

@section('title', 'Shipping task finished')

@section('icon', 'fas fa-shipping-fast ')

@section('back-link', url('/scanner/shipping'))

@section('content')
    <div class="col-12">
        @if( $task )
            <a href="{{ url('/scanner/shipping/' . $task->id ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Do next shipping job') }}
            </a>
            <a href="{{ url('/scanner/shipping/' ) }}" class="btn btn-primary btn-lg btn-block">
                {{ __('Show shipping tasks') }}
            </a>
        @else
            <p>{{__( 'All shipping tasks are done!')}}</p>
        @endif
        <a href="{{ url('/scanner/' ) }}" class="btn btn-primary btn-lg btn-block">
            {{ __('Go to start screen') }}
        </a>
    </div>
@endsection
