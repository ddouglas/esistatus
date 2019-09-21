<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="Red: {{ $stats->get('red') }} ({{ number_format(($stats->get('red') / $stats->get('total')) * 100, 2) }}%) || Yellow: {{ $stats->get('yellow') }} ({{ number_format(($stats->get('yellow') / $stats->get('total')) * 100, 2) }}%) || Green: {{ $stats->get('green') }} ({{ number_format(($stats->get('green') / $stats->get('total')) * 100, 2) }}%) || Total: {{ $stats->get('total') }} as of {{ Carbon\Carbon::now()->toDateTimeString() }}">
        <meta name="author" content="David Davaham">
        <link rel="icon" href="../../favicon.ico">

        <title>EVE Swagger Interface Endpoint Status || Eve Online</title>

        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">

        <!-- Custom styles for this template -->
        <link href="{{ url('css/app.css') }}" rel="stylesheet">

        @yield('css')
    </head>

    <body>

        <div class="container">
            <h1 class="text-center">Welcome to ESI Status</h1>
            <h3 class="text-center">A Simple <a href="{{ config('services.bitbucket.urls.repo') }}" target="_blank">Open Source Project</a> by David Davaham</h3>
            <hr />
            <p class="text-center">
                This site automatically refreshes every <strong>{{ $timer }}</strong> seconds.<br /> There are currently <strong><span id="countdown"></span></strong> seconds remaining till the next refresh
                <noscript><br />You need Javascript to refresh automatically</noscript>
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
                        <button type="submit" class="btn btn-primary btn-lg mt-2">Generate URL for selected endpoints</button>
                        <a href="{{ route('home') }}" class="btn btn-danger btn-lg mt-2">Reset Filter</a>
                    </div>
                </div>
            </form>
        </div>

        <hr>
        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        Brought to you by <a href="https://evewho.com/pilot/David+Davaham">David Davaham</a><br />
                        <p>Copyright &copy; <a href="mailto:ddouglas@douglaswebdev.net">David Douglas</a> - {{ now()->format('Y') }} </p>
                    </div>
                </div>
            </div>
        </footer>


        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

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


    </body>
</html>
