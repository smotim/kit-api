<?php

declare(strict_types=1);

namespace App\DTO;

use service\KitAPI\Model\Request\Geography\GetListCityRequest;

class CityByCodeDTO
{
    private ?int $id;
    private ?int $tdd_city_code;

    public function __construct(?int $tdd_city_code = null, ?int $id = null)
    {
        $this->tdd_city_code = $tdd_city_code;
        $this->id = $id;
    }

    public static function fromId(int $id): self
    {
        return new self(null, $id);
    }

    public static function fromCode(int $code): self
    {
        return new self($code);
    }

    public function toGetListCityRequest(): GetListCityRequest
    {
        $params = [];

        if ($this->id !== null) {
            $params['id'] = $this->id;
        }

        if ($this->tdd_city_code !== null) {
            $params['tdd_city_code'] = (string)$this->tdd_city_code;
        }

        return new GetListCityRequest(...$params);
    }
}