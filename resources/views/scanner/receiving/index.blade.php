@extends('layouts.scanner')

@section('title', 'Receiving')

@section('icon', 'fas fa-dolly')

@section('back-link', url('/scanner'))

@section('content')
    <div class="col-12">
        <div class="card text-center" style="margin-bottom: 15px;">
            <a href="{{ url('/scanner/receiving/order') }}" class="btn btn-primary btn-lg btn-block">
                {{__('Receive order')}}
            </a>
            <a href="{{ url('/scanner/receiving/cold') }}" class="btn btn-primary btn-lg btn-block">
                {{__('Receive cold')}}
            </a>
        </div>


    </div>
@endsection
