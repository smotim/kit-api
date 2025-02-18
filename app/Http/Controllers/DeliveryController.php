<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\KitDeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Http\Client\ClientExceptionInterface;

class DeliveryController
{
    private KitDeliveryService $kitService;

    /**
     * @param KitDeliveryService $kitService
     */
    public function __construct(KitDeliveryService $kitService)
    {
        $this->kitService = $kitService;
    }

    /**
     * @return JsonResponse
     * @throws ClientExceptionInterface
     */
    public function getAllTerminals(): JsonResponse
    {
        try {
            $terminals = $this->kitService->getTerminals();
            return response()->json(['data' => $terminals]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @param $cityId
     * @return JsonResponse
     * @throws ClientExceptionInterface
     */
    public function getCityTerminals($cityId): JsonResponse
    {
        try {
            $terminals = $this->kitService->getTerminals($cityId);
            return response()->json(['data' => $terminals]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 'message' => $e->getMessage()
            ], 422);
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
            $data = $request->all();
            $result = $this->kitService->calculateDelivery($data);
            return response()->json([
                'success' => true,
                'data' => [
                    'price' => $result->standart->cost,
                    'delivery_time' => $result->standart->time,
                    'details' => $result->standart->detail
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ClientExceptionInterface
     */
    public function searchCities(Request $request): JsonResponse
    {
        $query = $request->get('query');

        if (empty($query)) {
            return response()->json(null, 400);
        }

        try {
            $response = $this->kitService->searchCitiesByName($query);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
