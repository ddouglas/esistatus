@extends('layout.index')

@section('title', 'Default Layout')

@section('content')
    <div class="container">
        <h1 class="text-center">Welcome to ESI Status</h1>
        <h3 class="text-center">A Simple <a href="{{ config('services.bitbucket.urls.repo') }}" target="_blank">Open Source Project</a> by David Davaham</h3>
        <hr />
        <p class="text-center">
            This site automatically refreshes every <strong>{{ $timer }}</strong> seconds.<br /> There are currently <strong><span id="countdown"></span></strong> seconds remaining till the next refresh
        </p>
        @if ($lastUpdate < now()->subMinutes(2))
            <div class="alert alert-danger">
                <h3>Stale Information Warning</h3>
                <p>Status have not been updated since {{ $lastUpdate->toDateTimeString() }} and will be inaccurate.</p>
            </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header">
                        Red Endpoints
                    </div>
                    <div class="card-body">
                        <h1>
                            <span class="text-danger" id="#red">{{ $stats->get('red') }}</span>
                            @if($stats->get('total') > 0)
                                <small class="text-muted">({{ number_format(($stats->get('red') / $stats->get('total')) * 100, 2) }}%)</small>

                            @endif
                        </h1>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('home', ['status' => "red"]) }}" class="btn btn-block btn-danger">Filter By Red Status</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header">
                        Yellow Endpoints
                    </div>
                    <div class="card-body">
                        <h1>
                            <span class="text-warning" id="#yellow">{{ $stats->get('yellow') }}</span>
                            @if($stats->get('total') > 0)
                                <small class="text-muted">({{ number_format(($stats->get('yellow') / $stats->get('total')) * 100, 2) }}%)</small>
                            @endif
                        </h1>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('home', ['status' => "yellow"]) }}" class="btn btn-block btn-warning">Filter By Yellow Status</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-header ">
                        Green Endpoints
                    </div>
                    <div class="card-body">
                        <h1>
                            <span class="text-success" id="#green">{{ $stats->get('green') }}</span>
                            @if($stats->get('total') > 0)
                                <small class="text-muted">({{ number_format(($stats->get('green') / $stats->get('total')) * 100, 2) }}%)</small>
                            @endif
                        </h1>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('home', ['status' => "yellow,red"]) }}" class="btn btn-block btn-success">Filter Out Green Status</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr />
                <div class="float-right">
                    <small class="text-muted">Last Updated: {{ $lastUpdate->toDateString() }} at {{ $lastUpdate->toTimeString() }} UTC</small>
                </div>
                <h3>
                    Endpoint Statuses <small>[<a href="#" data-toggle="collapse" data-target=".multi-route">Expand Routes</a>]</small>
                </h3>
            </div>
        </div>
        <form action="{{ route('home') }}" method="post">
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
                    <div id="{{ $payload->get('group')->get('id') }}" class="collapse multi-route {{ $show }}" data-parent="#accordion">
                        <ul class="list-group list-group-flush">
                            @foreach($payload->get('endpoints') as $endpoint)
                                <?php
                                    $class = "";
                                    if ($endpoint->status === "red") {
                                        $class = "list-group-item-danger";
                                    } elseif ($endpoint->status === "yellow") {
                                        $class = "list-group-item-warning";
                                    }
                                ?>
                                <li class="list-group-item {{ $class }}">
                                    <label class="my-auto">
                                        <input type="checkbox" name="routes[{{ $endpoint->hash }}]" class="my-auto"/> {{ strtoupper($endpoint->method) }} {{ $endpoint->route }}
                                    </label>
                                </li>

                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
            <div class="row">
                <div class="col-12 mx-auto">
                    {{ csrf_field() }}
                    <button type="submit" class="btn-primary btn-lg mt-2">Generate URL for selected endpoints</button>
                    <a href="{{ route('home') }}" class="btn-danger btn-lg mt-2">Reset Filter</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        function countdown(remaining) {
            if(remaining === 0) {
        			location.reload(true);
            }
            document.getElementById('countdown').innerHTML = remaining;
            setTimeout(function(){ countdown(remaining - 1); }, 1000);
        };
        timer = {{ $refresh }};
        $(document).ready(function ()  {
            countdown(timer);
        });
    </script>
@endsection
