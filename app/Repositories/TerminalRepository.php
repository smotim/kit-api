<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Terminal;
use Illuminate\Support\Facades\DB;
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
        $query = $query ?? '';

        $result = DB::connection('mongodb')
            ->table('terminals')
            ->where(function($q) use ($query) {
                $q->where('city_name', 'like', '%' . $query . '%')
                    ->orWhere('address_code', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get();

        return $result->toArray();
    }
}
