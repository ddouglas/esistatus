<?php

namespace ESIS\Http\Controllers;

use Carbon, Request;
use ESIS\Status;

class HomeController extends Controller
{
    public function __construct ()
    {
        $this->http = new \GuzzleHttp\Client();
    }

    public function index()
    {
        $statuses = Status::get();
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

        $status = $payload->pluck('endpoints.*.status')->flatten();

        $stats = collect([
            'red' => $status->filter(function ($x) {return $x === "red";})->count(),
            'yellow' => $status->filter(function ($x) {return $x === "yellow";})->count(),
            'green' => $status->filter(function ($x) {return $x === "green";})->count(),
            'total' => $status->count()
        ]);

        $lastUpdate = Status::orderby('updated_at', 'desc')->first()->updated_at;

        return view('home', [
            'payload' => $payload,
            'stats' => $stats,
            'lastUpdate' => $lastUpdate
        ]);
    }
}
