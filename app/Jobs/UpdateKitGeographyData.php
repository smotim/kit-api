<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Terminal;
use App\Services\KitDeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateKitGeographyData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(KitDeliveryService $kitService)
    {
        try {
            Log::info('Starting synchronization with KIT API...');

            $start = microtime(true);

            $terminals = $kitService->getTerminals();
            $terminalIds = [];

            foreach ($terminals as $terminal) {
                if (is_object($terminal)) {
                    Terminal::updateOrCreate(
                        ['id' => $terminal->id],
                        [
                            'geography_city_id' => $terminal->geography_city_id,
                            'lat' => $terminal->lat,
                            'lon' => $terminal->lon,
                            'address_code' => $terminal->address_code,
                            'city_name' => $terminal->city_name,
                            'phone' => $terminal->phone,
                            'email' => $terminal->email,
                            'value' => $terminal->value,
                        ]
                    );
                    $terminalIds[] = $terminal->id;
                }
            }

            Terminal::whereNotIn('id', $terminalIds)->delete();

            $duration = round(microtime(true) - $start, 2);

            Log::info("Synchronization completed in {$duration} seconds");
        } catch (\Exception $e) {
            Log::error("Synchronization failed: {$e->getMessage()}");
            Log::error($e->getTraceAsString());
        }
    }
}
