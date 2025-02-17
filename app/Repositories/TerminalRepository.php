<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Terminal;
use MongoDB\Collection;

class TerminalRepository
{
    private Collection $collection;

    public function __construct()
    {
        $this->collection = Terminal::raw();
    }

    public function search(string $query, ?string $cityId = null): array
    {
        $filter = [
            '$or' => [
                ['value' => ['$regex' => $query, '$options' => 'i']],
                ['city_name' => ['$regex' => $query, '$options' => 'i']],
                ['address_code' => ['$regex' => $query, '$options' => 'i']]
            ]
        ];

        if ($cityId) {
            $filter['geography_city_id'] = $cityId;
        }

        return $this->collection
            ->find($filter, ['limit' => 10])
            ->toArray();
    }
}