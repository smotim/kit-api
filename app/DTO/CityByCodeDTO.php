<?php

declare(strict_types=1);

namespace App\DTO;

use service\KitAPI\Model\Request\Geography\GetListCityRequest;

class CityByCodeDTO
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function toGetListCityRequest(): GetListCityRequest
    {
        return new GetListCityRequest($this->code);
    }
}
