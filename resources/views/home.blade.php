@extends('layout.index')

@section('title', 'Default Layout')

@section('content')
    <div class="container">
        <h1 class="text-center">Welcome to ESI Status</h1>
        <h3 class="text-center">A Simple Open Source Project by David Davaham</h3>
        <hr />
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        Red Endpoints
                    </div>
                    <div class="card-body text-center">
                        <h1 class="text-danger">{{ $stats->get('red') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        Yellow Endpoints
                    </div>
                    <div class="card-body text-center">
                        <h1 class="text-warning">{{ $stats->get('yellow') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">
                        Green Endpoints
                    </div>
                    <div class="card-body text-center">
                        <h1 class="text-success">{{ $stats->get('green') }}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr />
                <h3>Endpoint Statuses</h3>
            </div>
        </div>
        @foreach($payload as $payload)
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0" data-toggle="collapse" data-target="#{{ $payload->get('group')->get('id') }}">
                        <div class="float-right">
                            R: {{ $payload->get('stats')->get('red') }}
                            Y: {{ $payload->get('stats')->get('yellow') }}
                            G: {{ $payload->get('stats')->get('green') }}
                        </div>
                        {{ $payload->get('group')->get('name') }}
                    </h5>
                </div>
                <div id="{{ $payload->get('group')->get('id') }}" class="collapse" data-parent="#accordion">
                    <ul class="list-group list-group-flush">
                        @foreach($payload->get('endpoints') as $endpoint)
                            @if($endpoint->status === "red")
                                <li class="list-group-item list-group-item-danger">{{ strtoupper($endpoint->method) }} {{ $endpoint->route }}</li>
                            @elseif ($endpoint->status === "yellow")
                                <li class="list-group-item list-group-item-warning">{{ strtoupper($endpoint->method) }} {{ $endpoint->route }}</li>
                            @else
                                <li class="list-group-item">{{ strtoupper($endpoint->method) }} {{ $endpoint->route }}</li>
                            @endif

                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@endsection
