<?php

namespace ESIS\Console\Commands;

use Illuminate\Console\Command;

use ESIS\Status;

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

        $statuses = Status::get()->keyBy('hash');

        $body->each(function ($endpoint) use ($statuses) {
            $status = $statuses->where('route', $endpoint->get('route'))->where('method', $endpoint->get('method'))->first();
            if (is_null($status)) {
                Status::create([
                    'route' => $endpoint->get('route'),
                    'method' => $endpoint->get('method'),
                    'hash' => str_limit(hash('sha1', $endpoint->get('method'). " ". $endpoint->get('route')), 6, ""),
                    'status' => $endpoint->get('status'),
                    'endpoint' => $endpoint->get('endpoint'),
                    'tags' => $endpoint->get('tags')->toJson()
                ]);
            } else {
                Status::where(['route' => $endpoint->get('route'), 'method' => $endpoint->get('method')])->update([
                    'status' =>  $endpoint->get('status')
                ]);
            }
            $statuses->forget($status->hash);
        });
        if ($statuses->isNotEmpty()) {
            $hashes = $statuses->keys();
            Status::whereIn('hash', $hashes->toArray())->delete();
        }
    }
}
