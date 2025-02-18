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

    /**
     * @param string|null $query
     * @return array
     */
    public function search(?string $query = null): array
    {
        $filter = [];

        if ($query) {
            $filter['$or'] = [
                ['city_name' => ['$regex' => $query, '$options' => 'i']],
                ['address_code' => ['$regex' => $query, '$options' => 'i']]
            ];
        }

        return $this->collection
            ->find($filter, ['limit' => 10])
            ->toArray();
    }
}
