<?php

namespace App\Console;

use App\Jobs\UpdateKitGeographyData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new UpdateKitGeographyData)
            ->monthly()
            ->at('00:00')
            ->withoutOverlapping();
    }
}