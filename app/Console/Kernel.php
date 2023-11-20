<?php

namespace App\Console;

use App\Http\Controllers\HiddenCourt\ClipController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function() {
            $clipController = new ClipController();
	    $token = $clipController->setClipToday();
	    Log::info($token);
        })->everyMinute();

        $schedule->command('queue:work --daemon --queue=high,default')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
