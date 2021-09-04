<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use  App\Device;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Device::dosabData();
        })->everyFifteenMinutes();
        if ((float)date("i") < 32) {
            $schedule->call(function () {
                Device::hourly();
            })->everyFiveMinutes();
        }

        $schedule->call(function () { 
            Device::virtualData();
            Device::remoteData();
        })->everyMinute(); 

        $schedule->call(function () {
          Device::diffData();
         })->everyMinute(); 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
