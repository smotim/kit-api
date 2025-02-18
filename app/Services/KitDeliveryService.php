<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\DeliveryCalculationDTO;
use Exception;
use service\KitAPI\Factory\SimpleClientFactory;
use service\KitAPI\Interfaces\ApiExceptionInterface;
use service\KitAPI\Interfaces\ClientExceptionInterface;
use service\KitAPI\Model\Entity\Order\CalculateResult;
use service\KitAPI\Model\Request\Geography\GetListAddressRequest;
use service\KitAPI\Model\Response\Tdd\SearchByNameResponse;

class KitDeliveryService
{
    private \service\KitAPI\KitAPIClient $client;

    public function __construct()
    {
        $this->client = SimpleClientFactory::createClient('https://capi.tk-kit.com', config('services.kit.token'));
    }

    /**
     * Get terminals in city
     *
     * @param string|null $cityId
     * @return array
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws Exception
     */
    public function getTerminals(string $cityId = null): array
    {
        try {
            $request = $cityId
                ? new GetListAddressRequest($cityId, true, true)
                : new GetListAddressRequest(withPhone: true, withEmail: true);
            $response = $this->client->geography->getListAddress($request);
            return $response->addreses;
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to get terminals: ' . $e->getMessage());
        }
    }

    /**
     * Calculate delivery cost
     *
     * @param array $data
     * @return CalculateResult
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function calculateDelivery(array $data): CalculateResult
    {
        try {
            $dto = DeliveryCalculationDTO::fromArray($data);
            $request = $dto->toCalculateRequest();
            $response = $this->client->order->calculate($request);
            return $response->getResult();
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to calculate delivery: ' . $e->getMessage());
        }
    }

    /**
     * Get list of countries
     *
     * @throws Exception|\Psr\Http\Client\ClientExceptionInterface
     */
    public function getServicesList(): \service\KitAPI\Model\Response\Order\GetListServiceResponse
    {
        try {
            return $this->client->order->getListService();
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to get services list: ' . $e->getMessage());
        }
    }

    /**
     * Search cities by name
     *
     * @param string $title
     * @return SearchByNameResponse
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function searchCitiesByName(string $title): \service\KitAPI\Model\Response\Tdd\SearchByNameResponse
    {
        try {
            $request = new \service\KitAPI\Model\Request\Tdd\SearchByNameRequest($title);
            return $this->client->tdd->searchByName($request);
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to search cities: ' . $e->getMessage());
        }
    }
}
