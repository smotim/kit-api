<?php

declare(strict_types=1);

namespace App\DTO;

use service\KitAPI\Model\Request\Tdd\SearchByNameRequest;

class SearchCitiesDTO
{
    private string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function toSearchByNameRequest(): SearchByNameRequest
    {
        return new SearchByNameRequest($this->query);
    }
}
