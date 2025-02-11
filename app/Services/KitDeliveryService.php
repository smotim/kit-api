<?php

declare(strict_types=1);

namespace App\Services;

use service\KitAPI\Factory\SimpleClientFactory;
use service\KitAPI\Interfaces\ApiExceptionInterface;
use service\KitAPI\Interfaces\ClientExceptionInterface;
use service\KitAPI\Model\Request\Order\CalculateRequest;
use service\KitAPI\Model\Entity\Order\Place;
use service\KitAPI\Model\Request\Geography\GetListAddressRequest;

class KitDeliveryService
{
    private \service\KitAPI\KitAPIClient $client;

    public function __construct()
    {
        $this->client = SimpleClientFactory::createClient('https://capi.tk-kit.com',config('services.kit.token') );
    }

    /**
     * Get list of cities
     *
     * @throws \Exception
     */
    public function getCities(): \service\KitAPI\Model\Response\Tdd\GetListCityResponse
    {
        try {
            return $this->client->tdd->getListCity();
        } catch (ApiExceptionInterface|ClientExceptionInterface $e) {
            throw new \Exception('Failed to get cities list: ' . $e->getMessage());
        }
    }

    /**
     * Get terminals in city
     *
     * @param string $cityId
     * @throws \Exception
     */
    public function getTerminals(string $cityId = null): array
    {
        try {
            if ($cityId) {
                $response = $this->client->geography->getListAddress(
                    new GetListAddressRequest($cityId, true, true)
                );
                return $response->addreses;
            } else {
                $response = $this->client->geography->getListAddress(
                    new GetListAddressRequest()
                );
                return $response->addreses;
            }
        } catch (ApiExceptionInterface|ClientExceptionInterface $e) {
            throw new \Exception('Failed to get terminals: ' . $e->getMessage());
        }
    }

    /**
     * Calculate delivery cost
     *
     * @param array $data
     * @throws \Exception
     */
    public function calculateDelivery(array $data): \service\KitAPI\Model\Entity\Order\CalculateResult
    {
        try {
            $request = new CalculateRequest();
            $request->city_pickup_code = $data['city_from'];
            $request->city_delivery_code = $data['city_to'];
            $request->declared_price = $data['declared_price'];

            $place = new Place();
            $place->height = $data['height'];
            $place->width = $data['width'];
            $place->length = $data['length'];
            $place->weight = $data['weight'];
            $place->volume = round(
                ($data['height'] * $data['width'] * $data['length']) / 1000000,
                3
            );
            $place->count_place = 1;

            $request->places = [$place];
            $request->delivery = 1;
            $request->currency_code = ['RUB'];

            $response = $this->client->order->calculate($request);
            return $response->getResult();
        } catch (ApiExceptionInterface|ClientExceptionInterface $e) {
            throw new \Exception('Failed to calculate delivery: ' . $e->getMessage());
        }
    }

    /**
     * Get list of countries
     *
     * @throws \Exception
     */
    public function getCountries(): \service\KitAPI\Model\Response\Tdd\GetListCountryResponse
    {
        try {
            return $this->client->tdd->getListCountry();
        } catch (ApiExceptionInterface|ClientExceptionInterface $e) {
            throw new \Exception('Failed to get countries list: ' . $e->getMessage());
        }
    }
    /**
     * Search cities by name
     *
     * @param string $title
     * @throws \Exception
     */
    public function searchCitiesByName(string $title): \service\KitAPI\Model\Response\Tdd\SearchByNameResponse
    {
        try {
            $request = new \service\KitAPI\Model\Request\Tdd\SearchByNameRequest($title);
            return $this->client->tdd->searchByName($request);
        } catch (ApiExceptionInterface|ClientExceptionInterface $e) {
            throw new \Exception('Failed to search cities: ' . $e->getMessage());
        }
    }
}
