<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\CityByCodeDTO;
use App\DTO\DeliveryCalculationDTO;
use App\DTO\KitTerminalsRequestDTO;
use App\DTO\SearchCitiesDTO;
use App\Services\KitDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class DeliveryController
{
    public function __construct(
        private KitDeliveryService $kitService
    ) {
    }

    /**
     * @return JsonResponse
     */
    public function getAllTerminals(): JsonResponse
    {
        try {
            $dto = new KitTerminalsRequestDTO();
            $terminals = $this->kitService->getTerminals($dto);
            return response()->json($terminals, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param string $cityId
     * @return JsonResponse
     */
    public function getCityTerminals(string $cityId): JsonResponse
    {
        try {
            $dto = new KitTerminalsRequestDTO($cityId);
            $terminals = $this->kitService->getTerminals($dto);
            return response()->json($terminals, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function calculateDelivery(Request $request): JsonResponse
    {
        try {
            $dto = DeliveryCalculationDTO::fromArray($request->all());
            $result = $this->kitService->calculateDelivery($dto);
            return response()->json([
                'data' => $result
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchCities(Request $request): JsonResponse
    {
        if (!$request->has('query')) {
            return response()->json([
                'message' => 'Search query is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $dto = new SearchCitiesDTO($request->get('query'));
            $cities = $this->kitService->searchCitiesByName($dto);
            return response()->json($cities, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $code
     * @return JsonResponse
     */
    public function getCityByCode(string $code): JsonResponse
    {
        try {
            $dto = new CityByCodeDTO($code);
            $city = $this->kitService->getCityByCode($dto);
            return response()->json($city, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
