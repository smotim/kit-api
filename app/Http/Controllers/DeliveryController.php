<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use App\Services\KitDeliveryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DeliveryController extends Controller
{
    private KitDeliveryService $kitService;

    public function __construct(KitDeliveryService $kitService)
    {
        $this->kitService = $kitService;
    }

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

    public function calculateDelivery(Request $request): JsonResponse
    {
        try {
            // Convert request to array and get all input data
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
