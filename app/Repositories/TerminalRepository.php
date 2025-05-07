<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class TerminalRepository
{
    private const CACHE_KEY_PREFIX = 'terminals:';
    private const CACHE_ALL_KEY = 'terminals:all';

    /**
     * Search terminals by query string
     *
     * @param string|null $query
     * @return array
     */
    public function search(?string $query = null): array
    {
        $query = $query ?? '';

        if (empty($query)) {
            return $this->getAllTerminals();
        }

        $allTerminals = $this->getAllTerminals();

        return array_filter($allTerminals, function ($terminal) use ($query) {
            $query = strtolower($query);
            return stripos(strtolower($terminal['city_name'] ?? ''), $query) !== false ||
                   stripos(strtolower($terminal['address_code'] ?? ''), $query) !== false;
        });
    }

    /**
     * Get all terminals from cache
     *
     * @return array
     */
    public function getAllTerminals(): array
    {
        return Cache::get(self::CACHE_ALL_KEY, []);
    }

    /**
     * Cache terminals from API
     *
     * @param array $terminals
     * @return void
     */
    public function cacheTerminals(array $terminals): void
    {
        $this->clearCache();

        Cache::forever(self::CACHE_ALL_KEY, $terminals);

        foreach ($terminals as $terminal) {
            if (isset($terminal['id'])) {
                Cache::forever(
                    self::CACHE_KEY_PREFIX . $terminal['id'],
                    $terminal
                );
            }
        }
        Log::info('Сохранено ' . count($terminals) . ' терминалов без ограничения времени жизни');
    }

    /**
     * Get terminal by ID
     *
     * @param string $id
     * @return array|null
     */
    public function find(string $id): ?array
    {
        return Cache::get(self::CACHE_KEY_PREFIX . $id);
    }

    /**
     * Clear terminal cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_ALL_KEY);

        $prefix = config('cache.prefix') ? config('cache.prefix') . '_' : '';
        $keys = Redis::keys("{$prefix}cache_" . self::CACHE_KEY_PREFIX . "*");

        foreach ($keys as $key) {
            Redis::del($key);
        }

        Log::info('Кеш терминалов полностью очищен, удалено ' . count($keys) . ' ключей');
    }
}
