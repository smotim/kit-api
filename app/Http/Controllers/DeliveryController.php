<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\KitDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

readonly class DeliveryController
{
    public function __construct(
        private KitDeliveryService $kitService
    ) {}

    /**
     * @return JsonResponse
     * @throws ClientExceptionInterface
     */
    public function getAllTerminals(): JsonResponse
    {
        try {
            $terminals = $this->kitService->getTerminals();
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
     * @throws ClientExceptionInterface
     */
    public function getCityTerminals(string $cityId): JsonResponse
    {
        try {
            $terminals = $this->kitService->getTerminals($cityId);
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
     * @throws ClientExceptionInterface
     */
    public function calculateDelivery(Request $request): JsonResponse
    {
        try {
            $result = $this->kitService->calculateDelivery($request->all());
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
     * @throws ClientExceptionInterface
     */
    public function searchCities(Request $request): JsonResponse
    {
        if (!$request->has('query')) {
            return response()->json([
                'message' => 'Search query is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $cities = $this->kitService->searchCitiesByName($request->get('query'));
            return response()->json($cities, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}