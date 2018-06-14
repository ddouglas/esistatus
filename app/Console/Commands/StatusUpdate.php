<?php

namespace ESIS\Console\Commands;

use Illuminate\Console\Command;

class StatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'status:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ping ESI and get the latest status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->http = new \GuzzleHttp\Client();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->http->get('https://esi.evetech.net/status.json');

        $statusCode = $response->getStatusCode();
        if ($statusCode != 200) {
            return;
        }
        $body = collect(json_decode($response->getBody()))->recursive();

        $body->each(function ($endpoint) {
            $status = Status::where(['route' => $endpoint->get('route'), 'method' => $endpoint->get('method')])->first();
            if (!$status->exists) {
                Status::create([
                    'route' => $endpoint->get('route'),
                    'method' => $endpoint->get('method'),
                    'status' => $endpoint->get('status'),
                    'endpoint' => $endpoint->get('endpoint'),
                    'tags' => $endpoint->get('tags')->toJson()
                ]);
            } else {
                Status::where(['route' => $endpoint->get('route'), 'method' => $endpoint->get('method')])->update([
                    'status' =>  $endpoint->get('status')
                ]);
            }
        });
    }
}
