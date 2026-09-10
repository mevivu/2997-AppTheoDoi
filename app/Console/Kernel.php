<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('package:update-expired')->everyMinute();
        $schedule->command('otp:clean')->daily();
        $schedule->command('notification:send-push')->everyMinute()->withoutOverlapping();
        $schedule->command('session:clean-deleted --days=0')->everyMinute();
        $schedule->command('notification:clean-old --days=7')->dailyAt('02:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
