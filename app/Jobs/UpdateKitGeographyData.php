<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repositories\TerminalRepository;
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

    public function handle(KitDeliveryService $kitService, TerminalRepository $terminalRepository)
    {
        try {
            Log::info('Starting synchronization with KIT API...');

            $start = microtime(true);

            $terminals = $kitService->getTerminals();
            Log::info('Retrieved ' . count($terminals) . ' terminals from API');

            if (count($terminals) === 0) {
                Log::warning('No terminals returned from KIT API');
                return;
            }

            $processedTerminals = [];

            foreach ($terminals as $terminal) {
                if (is_object($terminal)) {
                    // Convert object to array for caching
                    $processedTerminals[] = [
                        'id' => $terminal->id,
                        'geography_city_id' => $terminal->geography_city_id,
                        'lat' => $terminal->lat,
                        'lon' => $terminal->lon,
                        'address_code' => $terminal->address_code,
                        'city_name' => $terminal->city_name,
                        'phone' => $terminal->phone,
                        'email' => $terminal->email,
                        'value' => $terminal->value,
                    ];
                }
            }

            Log::info('Processed ' . count($processedTerminals) . ' terminals for caching');

            // Store all terminals in cache
            $terminalRepository->cacheTerminals($processedTerminals);

            // Verify data was cached by reading it back
            $cachedCount = count($terminalRepository->getAllTerminals());
            Log::info("Verification: found {$cachedCount} terminals in cache after storing");

            $duration = round(microtime(true) - $start, 2);

            Log::info("Synchronization completed in {$duration} seconds. Cached " . count($processedTerminals) . " terminals.");
        } catch (\Exception $e) {
            Log::error("Synchronization failed: {$e->getMessage()}");
            Log::error($e->getTraceAsString());
        }
    }
}