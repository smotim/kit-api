<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\CityByCodeDTO;
use App\DTO\DeliveryCalculationDTO;
use App\DTO\KitTerminalsRequestDTO;
use App\DTO\SearchCitiesDTO;
use Exception;
use InvalidArgumentException;
use service\KitAPI\Factory\SimpleClientFactory;
use service\KitAPI\Interfaces\ApiExceptionInterface;
use service\KitAPI\Interfaces\ClientExceptionInterface;
use service\KitAPI\KitAPIClient;
use service\KitAPI\Model\Entity\Order\CalculateResult;
use service\KitAPI\Model\Response\Geography\GetListCityResponse;
use service\KitAPI\Model\Response\Order\GetListServiceResponse;
use service\KitAPI\Model\Response\Tdd\SearchByNameResponse;
use Symfony\Component\HttpFoundation\Response;

class KitDeliveryService
{
    private KitAPIClient $client;

    public function __construct()
    {
        $this->client = SimpleClientFactory::createClient('https://capi.tk-kit.com', config('services.kit.token'));
    }

    /**
     * Get terminals in the city
     *
     * @param KitTerminalsRequestDTO $dto
     * @return array
     * @throws Exception
     */
    public function getTerminals(KitTerminalsRequestDTO $dto): array
    {
        try {
            $response = $this->client->geography->getListAddress($dto->toGetListAddressRequest());
            return $response->addreses;
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to get terminals: ' . $e->getMessage());
        }
    }

    /**
     * Calculate delivery cost
     *
     * @param DeliveryCalculationDTO $dto
     * @return CalculateResult
     * @throws Exception
     */
    public function calculateDelivery(DeliveryCalculationDTO $dto): CalculateResult
    {
        try {
            return $this->client->order->calculate($dto->toCalculateRequest())->getResult();
        } catch (InvalidArgumentException $e) {
            throw new Exception($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to calculate delivery: ' . $e->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
    }

    /**
     * Get a list of countries
     *
     * @return GetListServiceResponse
     * @throws Exception
     */
    public function getServicesList(): GetListServiceResponse
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
     * @param SearchCitiesDTO $dto
     * @return SearchByNameResponse
     * @throws Exception
     */
    public function searchCitiesByName(SearchCitiesDTO $dto): SearchByNameResponse
    {
        try {
            return $this->client->tdd->searchByName($dto->toSearchByNameRequest());
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to search cities: ' . $e->getMessage());
        }
    }

    /**
     * Get City by Code
     *
     * @param CityByCodeDTO $dto
     * @return GetListCityResponse
     * @throws Exception
     */
    public function getCityByCode(CityByCodeDTO $dto): GetListCityResponse
    {
        try {
            return $this->client->geography->getListCity($dto->toGetListCityRequest());
        } catch (ApiExceptionInterface | ClientExceptionInterface $e) {
            throw new Exception('Failed to search cities: ' . $e->getMessage());
        }
    }
}
