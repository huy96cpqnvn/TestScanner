<?php

namespace App\Console;

use App\Models\RequestSendMail;
use App\Console\Commands\CheckUser;
use App\Jobs\CreateInvestorSipLineJob;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\UpdateTradingSessionEndJob;
use App\Jobs\CreateTradingOrderBuySIPJob;
use App\Jobs\UpdateTradingSessionTimeUpJob;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\RequestSendMailCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Console\Commands\RequestGetListFlightCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RequestSendMailCommand::class,
        RequestGetListFlightCommand::class
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:RequestSendMailCommand')->everyMinute();
        $schedule->command('command:RequestGetListFlightCommand')->daily();

        // $schedule->job(new UpdateTradingSessionEndJob(), 'schedules', config('queue.default'))->withoutOverlapping()->daily();
        // // tạo lệnh mua SIP
        // $schedule->job(new UpdateTradingSessionTimeUpJob(), 'schedules', config('queue.default'))->withoutOverlapping()->everyMinute();
        // $schedule->job(new CreateTradingOrderBuySIPJob(), 'schedules', config('queue.default'))->withoutOverlapping()->everyMinute();
        // // tạo SIP Line vào ngày đầu tháng lúc 00:00
        // $schedule->job(new CreateInvestorSipLineJob(), 'schedules', config('queue.default'))->withoutOverlapping()->monthly();

        // $schedule->job(new GetListFlightsJob(), 'schedules', config('queue.default'))->withoutOverlapping()->dailyAt('00:00');
    }
}
