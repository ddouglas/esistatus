<?php

namespace ESIS\Http\Controllers;

use Carbon, Request, Validator;
use ESIS\Status;

class HomeController extends Controller
{
    public function __construct ()
    {
        $this->http = new \GuzzleHttp\Client();
    }

    public function index()
    {
        if (Request::isMethod('post')) {
            $validator = Validator::make(Request::all(), [
                'routes' => 'required|array',

            ]);
            if ($validator->fails()) {
                return redirect(route('home'))->withErrors($validator);
            }
            $routes = collect(Request::get('routes'))->keys();
            $exists = Status::whereIn('hash', $routes)->get();
            foreach($routes as $route) {
                if ($exists->where('hash', $route)->isEmpty()) {
                    return redirect(route('home'));
                }
            }
            $routes = $routes->implode(',');
            $params = collect(['endpoints' => $routes]);
            if (Request::has('status')) {
                $params = $params->put('status', Request::get('status'));
            }
            return redirect(route('home', $params->toArray()));
        }
        $show = "";
        $statuses = Status::get();

        $stats = collect([
            'red' => $statuses->where('status', 'red')->count(),
            'yellow' => $statuses->where('status', 'yellow')->count(),
            'green' => $statuses->where('status', 'green')->count(),
            'total' => $statuses->count()
        ]);

        if (Request::has('endpoints')) {
            $show = "show";
            $statuses = $statuses->whereIn('hash', collect(explode(',', Request::get('endpoints'))));
            $stats = collect([
                'red' => $statuses->where('status', 'red')->count(),
                'yellow' => $statuses->where('status', 'yellow')->count(),
                'green' => $statuses->where('status', 'green')->count(),
                'total' => $statuses->count()
            ]);
        } else if (Request::has('status')) {
            $show = "show";
            $statuses = $statuses->whereIn('status', collect(explode(',', Request::get('status'))));
        }

        $payload = collect();

        $statuses->each(function ($endpoint) use ($payload) {
            $name = str_replace('-', '_', $endpoint->endpoint);
            if (!$payload->has($name)) {
                $payload->put($name, collect([
                    'group' => collect([
                        'name' =>  $endpoint->tags->first(),
                        'id' => $name
                    ]),
                    'stats' => collect([
                        'red' => 0,
                        'yellow' => 0,
                        'green' => 0
                    ]),
                    'endpoints' => collect()
                ]));

            }

            $payload->get($name)->get('stats')->put($endpoint->status, $payload->get($name)->get('stats')->get($endpoint->status) + 1);
            $payload->get($name)->get('endpoints')->push($endpoint);
        });

        $lastUpdate = Status::orderby('updated_at', 'desc')->first()->updated_at;

        $timer = 60;

        if ($timer - now()->format("s") < 0) {
            return redirect(route('home'));
        } else {
            $refresh = $timer - now()->format("s");
        }

        return view('home', [
            'payload' => $payload,
            'stats' => $stats,
            'lastUpdate' => $lastUpdate,
            'show' => $show,
            'timer' => $timer,
            'refresh' => $refresh
        ]);
    }
}
