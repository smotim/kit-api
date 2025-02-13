<?php

namespace App\Jobs;

use App\Services\KitDeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateKitGeographyData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(KitDeliveryService $kitService)
    {
        \Log::info('Starting KIT geography data update');
        $kitService->updateGeographyData();
        \Log::info('KIT geography data update completed');
    }
}
