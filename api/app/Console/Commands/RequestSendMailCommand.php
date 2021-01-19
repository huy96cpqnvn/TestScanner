<?php

/**
 * Created by PhpStorm.
 * User: tungnd
 * Date: 02/04/2019
 * Time: 14:43
 */

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\RequestSendMail;
use Illuminate\Console\Command;
use App\Jobs\ProcessRequestSendMailJob;
use App\Models\File;

class RequestSendMailCommand extends Command
{
    protected $name = 'command:RequestSendMailCommand';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * @return mixed
     */
    public function handle()
    {
        $now = date("Y-m-d H:i", strtotime(Carbon::now()->addHour()));
        $requestSendMailApplys = RequestSendMail::where('status', RequestSendMail::STATUS_NEW)->get();
        $requestSendMailApplys->where('send_at', '>', $now)->each(function ($item) {
            $item->status = RequestSendMail::STATUS_PROCESSING;
            $item->save();
            //Lay ra files
            $files = File::where('ref_id', $item['id'])
                ->where('ref_type', RequestSendMail::class)
                ->select('path')
                ->get();
            $files = json_encode($files);
            $arrToEmail = explode(", ", $item->to_emails);
            foreach ($arrToEmail as $to_email) {
                $job = (new ProcessRequestSendMailJob($item, $to_email, $files))->onConnection(config('queue.default'))->onQueue('notifications');
                dispatch($job);
            }
        });
    }
}
