<?php
/**
 * Created by PhpStorm.
 * User: tungnd
 * Date: 02/04/2019
 * Time: 14:43
 */
namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserStatistic;
use Illuminate\Console\Command;

class CheckUser extends Command
{
    protected $name = 'command:checkUser';

    protected $description = 'Check active/deactivate time of user for statistic';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $date = $dfd ->were=
    }



}
