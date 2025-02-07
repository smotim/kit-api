<?php

namespace App\Services;

use Smotim\KitAPI\Client;
use Smotim\KitAPI\Service\CalculatorService;
use Smotim\KitAPI\Service\GeographyService;
use App\Models\City;
use App\Models\Terminal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KitDeliveryService
{
    private Client $client;
    private CalculatorService $calculator;
    private GeographyService $geography;

    public function __construct()
    {
        $this->client = new Client(
            config('services.kit.token'),
            config('services.kit.url')
        );

        $this->calculator = new CalculatorService($this->client);
        $this->geography = new GeographyService($this->client);
    }

    public function calculateDelivery(array $params): array
    {
        try {
            return $this->calculator->calculate($params);
        } catch (\Exception $e) {
            Log::error('KIT delivery calculation error', [
                'params' => $params,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function updateGeographyData(): void
    {
        try {
            $startTime = microtime(true);
            Log::info('Starting geography data update');

            $cities = collect($this->geography->getCities());
            $terminals = collect($this->geography->getTerminals());

            // Bulk update cities
            $citiesData = $cities->map(function ($cityData) {
                return [
                    'city_id' => $cityData['id'],
                    'name' => $cityData['name'],
                    'region' => $cityData['region'] ?? null,
                    'terminals_count' => $cityData['terminals_count'] ?? 0,
                    'updated_at' => now()
                ];
            })->toArray();

            City::raw()->bulkWrite(
                array_map(function ($city) {
                    return [
                        'updateOne' => [
                            ['city_id' => $city['city_id']],
                            ['$set' => $city],
                            ['upsert' => true]
                        ]
                    ];
                }, $citiesData)
            );

            // Bulk update terminals
            $terminalsData = $terminals->map(function ($terminalData) {
                return [
                    'terminal_id' => $terminalData['id'],
                    'city_id' => $terminalData['city_id'],
                    'name' => $terminalData['name'],
                    'address' => $terminalData['address'],
                    'phone' => $terminalData['phone'] ?? null,
                    'working_hours' => $terminalData['working_hours'] ?? null,
                    'coordinates' => [
                        'lat' => $terminalData['latitude'] ?? null,
                        'lng' => $terminalData['longitude'] ?? null,
                    ],
                    'updated_at' => now()
                ];
            })->toArray();

            Terminal::raw()->bulkWrite(
                array_map(function ($terminal) {
                    return [
                        'updateOne' => [
                            ['terminal_id' => $terminal['terminal_id']],
                            ['$set' => $terminal],
                            ['upsert' => true]
                        ]
                    ];
                }, $terminalsData)
            );

            $duration = round(microtime(true) - $startTime, 2);
            Log::info("Geography data update completed", [
                'duration' => $duration,
                'cities_count' => count($citiesData),
                'terminals_count' => count($terminalsData)
            ]);

        } catch (\Exception $e) {
            Log::error('KIT geography update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}