<?php

namespace App\Console\Commands;

use App\Jobs\UpdateKitGeographyData;
use Illuminate\Console\Command;

class SyncKitGeography extends Command
{
    protected $signature = 'kit:sync-geography';
    protected $description = 'Synchronize KIT geography data';

    public function handle()
    {
        UpdateKitGeographyData::dispatch();
        $this->info('Synchronization job dispatched');
    }
}