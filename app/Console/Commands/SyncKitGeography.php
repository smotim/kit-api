<?php

namespace App\Console\Commands;

use App\Services\KitDeliveryService;
use Illuminate\Console\Command;
use App\Models\Terminal;

class SyncKitGeography extends Command
{
    protected $signature = 'kit:sync-geography';
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
            $this->info('Starting synchronization with KIT API...');

            $start = microtime(true);

            $terminals = $this->kitService->getTerminals();
            $terminalIds = [];

            foreach ($terminals as $terminal) {
                if (is_object($terminal)) {
                    $this->syncTerminal($terminal);
                    $terminalIds[] = $terminal->id;
                }
            }


            $deleted = Terminal::whereNotIn('id', $terminalIds)->delete();
            $this->info("Updated: " . count($terminalIds) . " terminals, Deleted: {$deleted}");


            $duration = round(microtime(true) - $start, 2);


            $this->info("Synchronization completed in {$duration} seconds");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Synchronization failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }

    private function syncTerminal(object $terminal): void
    {
        Terminal::updateOrCreate(
            ['id' => $terminal->id ?? null],
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
    }
}
