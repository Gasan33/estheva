<?php

namespace App\Console;

use App\Console\Commands\UpdateTimeSlots;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        UpdateTimeSlots::class, // Register the custom command
    ];
    protected function schedule(Schedule $schedule)
    {
        // Define scheduled tasks here
        $schedule->command('app:deactivate-expired-promo-codes')->daily();
        $schedule->command('timeslots:update')->dailyAt('00:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
