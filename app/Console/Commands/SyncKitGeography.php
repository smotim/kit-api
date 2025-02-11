<?php

namespace App\Console\Commands;

use App\Services\KitDeliveryService;
use Illuminate\Console\Command;
use App\Models\Terminal;

class SyncKitGeography extends Command
{
    protected $signature = 'kit:sync-geography {--force : Force sync even if last sync was recent}';
    protected $description = 'Synchronize cities and terminals data from KIT API';

    private KitDeliveryService $kitService;

    public function __construct(KitDeliveryService $kitService)
    {
        parent::__construct();
        $this->kitService = $kitService;
    }

    public function handle()
    {
        try {
            $lastSync = cache()->get('last_kit_sync');
            $now = now();

            if (!$this->option('force') && $lastSync && $now->diffInHours($lastSync) < 24) {
                $this->warn('Last sync was less than 24 hours ago. Use --force to sync anyway.');
                return 1;
            }

            $this->info('Starting synchronization with KIT API...');

            $start = microtime(true);

            // Fetch terminals
            $terminals = $this->kitService->getTerminals();

            // Save terminals to MongoDB
            foreach ($terminals as $cityName => $cityTerminals) {
                foreach ($cityTerminals as $terminal) {
                    if (is_object($terminal)) {
                        Terminal::updateOrCreate(
                            ['id' => $terminal->id],
                            [
                                'geography_city_id' => $terminal->geography_city_id,
                                'lat' => $terminal->lat,
                                'lon' => $terminal->lon,
                                'address_code' => $terminal->address_code,
                                'cityName' => $cityName,
                                'phones' => $terminal->phones,
                                'emails' => $terminal->emails,
                                'value' => $terminal->value,
                            ]
                        );
                    }
                }
            }

            $duration = round(microtime(true) - $start, 2);

            // Save the last sync time
            cache()->put('last_kit_sync', $now, now()->addDays(30));

            $this->info("Synchronization completed in {$duration} seconds");

            return 0;

        } catch (\Exception $e) {
            $this->error("Synchronization failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
