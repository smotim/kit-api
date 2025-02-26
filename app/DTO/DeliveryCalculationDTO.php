<?php

declare(strict_types=1);

namespace App\DTO;

use service\KitAPI\Model\Entity\Order\Place;
use service\KitAPI\Model\Request\Order\CalculateRequest;

class DeliveryCalculationDTO
{
    /** @var string */
    private string $cityFrom;

    /** @var string */
    private string $cityTo;

    /** @var int */
    private int $declaredPrice;

    /** @var array */
    private array $places;

    /** @var int|null */
    private ?int $confirmationPrice = null;

    /** @var int|null */
    private ?int $haveDoc = null;

    /** @var int|null */
    private ?int $insurance = null;

    /** @var string|null */
    private ?string $insuranceAgentCode = null;

    /** @var int */
    private int $pickUp = 1;

    /** @var int */
    private int $delivery = 1;

    /** @var string */
    private string $cargoTypeCode = '03';

    /** @var array */
    private array $currencyCode = ['RUB'];

    /** @var int */
    private int $allPlacesSame = 0;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->cityFrom = $data['city_delivery_code'];
        $dto->cityTo = $data['city_pickup_code'];
        $dto->declaredPrice = $data['declared_price'];
        $dto->places = $data['places'];

        if (isset($data['pick_up'])) {
            $dto->pickUp = $data['pick_up'];
        }
        if (isset($data['delivery'])) {
            $dto->delivery = $data['delivery'];
        }
        if (isset($data['cargo_type_code'])) {
            $dto->cargoTypeCode = $data['cargo_type_code'];
        }
        if (isset($data['currency_code'])) {
            $dto->currencyCode = $data['currency_code'];
        }
        if (isset($data['all_places_same'])) {
            $dto->allPlacesSame = $data['all_places_same'];
        }

        return $dto;
    }

    public function toCalculateRequest(): CalculateRequest
    {
        $request = new CalculateRequest();
        $request->city_pickup_code = $this->cityFrom;
        $request->city_delivery_code = $this->cityTo;
        $request->declared_price = $this->declaredPrice;
        $request->pick_up = $this->pickUp;
        $request->delivery = $this->delivery;
        $request->cargo_type_code = $this->cargoTypeCode;
        $request->currency_code = $this->currencyCode;
        $request->all_places_same = $this->allPlacesSame;

        if ($this->declaredPrice >= 50000) {
            $request->confirmation_price = $this->confirmationPrice ?? 1;
            $request->have_doc = $this->haveDoc ?? 1;
        }

        if ($this->declaredPrice >= 10000) {
            $request->insurance = $this->insurance ?? 1;
            if ($request->insurance === 1) {
                $request->insurance_agent_code = $this->insuranceAgentCode;
            }
        }

        $request->places = array_map(function ($placeData) {
            $place = new Place();
            if (isset($placeData['volume'])) {
                $place->volume = $placeData['volume'];
            } else {
                $place->height = $placeData['height'];
                $place->width = $placeData['width'];
                $place->length = $placeData['length'];
            }

            $place->weight = $placeData['weight'];
            $place->count_place = $placeData['count_place'];

            if (isset($placeData['service'])) {
                $place->service = $placeData['service'];
            }

            return $place;
        }, $this->places);

        return $request;
    }
}
