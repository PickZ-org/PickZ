@extends('layouts.scanner')

@section('title', 'Overview')

@section('icon', 'fas fa-tasks')


@section('content')
    <div class="col-12 dashboard-scanner">
        <div class="card text-center card-gray" style="margin-bottom: 15px;">
            <div class="card-header">
                <h4 class="card-title w-100">Inbound</h4>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <a href="{{ url('/scanner/receiving') }}" class="btn btn-primary btn-lg btn-block">
                        Receiving
                    </a>
                    <a href="{{ url('/scanner/putaway') }}" class="btn btn-primary btn-lg btn-block">
                        Putaway <span class="badge badge-warning">{{$count_putaway}}</span>
                    </a>
                    <a href="{{ url('/scanner/crossdock') }}" class="btn btn-primary btn-lg btn-block">
                        Crossdock <span class="badge badge-warning">{{$count_crossdock}}</span>
                    </a>
                </p>
            </div>
        </div>

        <div class="card text-center card-gray" style="margin-bottom: 15px;">
            <div class="card-header">
                <h4 class="card-title w-100">Outbound</h4>
            </div>
            <div class="card-body">
                <p class="card-text">
                    @if( ! \Configuration::get('pick_from_bulk', false)  )
                        <a href="{{ url('/scanner/replenishment') }}" class="btn btn-primary btn-lg btn-block">
                            Replenishment <span
                                class="badge badge-warning">{{$count_replenishment}}</span>
                        </a>
                    @endif

                    <a href="{{ url('/scanner/picking') }}" class="btn btn-primary btn-lg btn-block">
                        Picking <span class="badge badge-warning">{{$count_pick}}</span>
                    </a>

                    <a href="{{ url('/scanner/shipping') }}" class="btn btn-primary btn-lg btn-block">
                        Shipping <span class="badge badge-warning">{{$count_shipping}}</span>
                    </a>
                </p>
            </div>
        </div>

        <div class="card text-center card-gray">
            <div class="card-header">
                <h4 class="card-title w-100">Misc</h4>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <a href="{{ url('/scanner/move') }}" class="btn btn-primary btn-lg btn-block">
                        Move stock <span class="badge badge-warning">{{$count_move}}</span>
                    </a>
                </p>
            </div>
        </div>

    </div>
@endsection
