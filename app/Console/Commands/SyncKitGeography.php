<?php

namespace App\Console\Commands;

use App\Services\KitDeliveryService;
use Illuminate\Console\Command;

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

            // Выполняем синхронизацию
            $this->kitService->updateGeographyData();

            $duration = round(microtime(true) - $start, 2);

            // Сохраняем время последней синхронизации
            cache()->put('last_kit_sync', $now, now()->addDays(30));

            $this->info("Synchronization completed in {$duration} seconds");

            // Выводим статистику
            $citiesCount = \App\Models\City::count();
            $terminalsCount = \App\Models\Terminal::count();

            $this->table(
                ['Entity', 'Count'],
                [
                    ['Cities', $citiesCount],
                    ['Terminals', $terminalsCount],
                ]
            );

            return 0;

        } catch (\Exception $e) {
            $this->error("Synchronization failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}