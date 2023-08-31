<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use  App\Device;
use App\DeviceData;
use App\Triger;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\sendReportsMail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        try {
            $schedule->call(function () {
                DeviceData::deleteOldData(93);
            })->everyThirtyMinutes();
        } catch (\Throwable $th) {
            log::error($th->getMessage(),$th->getTrace());
        }

        try {
            $schedule->call(function () {
                Device::fillHourly();
            })->hourly();
        } catch (\Throwable $th) {
            log::error($th->getMessage(),$th->getTrace());
        }

        try {
            if ((float)date("i") < 32) {
                $schedule->call(function () {
                    Device::hourly();
                })->everyFiveMinutes();
            }
        } catch (\Throwable $th) {
            log::error($th->getMessage(),$th->getTrace());
        }

        try {
            $schedule->call(function () {
                Device::virtualData();
                Device::remoteData();
            })->everyMinute();
        } catch (\Throwable $th) {
            log::error($th->getMessage(),$th->getTrace());
        }


        try {
            $schedule->call(function () {
                Triger::check();
            })->everyMinute();
        } catch (\Throwable $th) {
            log::error($th->getMessage(),$th->getTrace());
        }

        try {
            $schedule->call(function () {
                Device::diffData();
            })->everyMinute();
        } catch (\Throwable $th) {
            log::error($th->getMessage(),$th->getTrace());
        }

        $schedule->command('mail:reports')
        ->everyFifteenMinutes();

        $schedule->call(function () {
            $root_path = base_path();
            $process = new Process('cd ' . $root_path . '; ./deploy.sh');
            $process->run(function ($type, $buffer) {
                Log::info("deploy : $buffer");
            });
        })->dailyAt('02:44');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
