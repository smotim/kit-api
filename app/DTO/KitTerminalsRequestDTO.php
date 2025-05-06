<?php

declare(strict_types=1);

namespace App\DTO;

use service\KitAPI\Model\Request\Geography\GetListAddressRequest;

class KitTerminalsRequestDTO
{
    private ?string $cityId;
    private bool $withPhone;
    private bool $withEmail;

    public function __construct(?string $cityId = null, bool $withPhone = true, bool $withEmail = true)
    {
        $this->cityId = $cityId;
        $this->withPhone = $withPhone;
        $this->withEmail = $withEmail;
    }

    public function toGetListAddressRequest(): GetListAddressRequest
    {
        if ($this->cityId) {
            return new GetListAddressRequest($this->cityId, $this->withPhone, $this->withEmail);
        }

        return new GetListAddressRequest(withPhone: $this->withPhone, withEmail: $this->withEmail);
    }
}
