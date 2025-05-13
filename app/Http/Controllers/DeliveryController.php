<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\CityByCodeDTO;
use App\DTO\DeliveryCalculationDTO;
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
     * @param Request $request
     * @return JsonResponse
     */
    public function getTerminals(Request $request): JsonResponse
    {
        $geography_city_id = $request->input('geography_city_id');
        $withPhone = filter_var($request->input('withPhone', false), FILTER_VALIDATE_BOOLEAN);
        $withEmail = filter_var($request->input('withEmail', false), FILTER_VALIDATE_BOOLEAN);

        try {
            $terminals = $this->kitService->getTerminals($geography_city_id, $withPhone, $withEmail);
            return response()->json($terminals, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
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
     * @param int $tdd_city_code
     * @return JsonResponse
     */
    public function getCityByCode(int $tdd_city_code): JsonResponse
    {
        try {
            $dto = new CityByCodeDTO($tdd_city_code);
            $city = $this->kitService->getCityByCode($dto);
            return response()->json($city, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
