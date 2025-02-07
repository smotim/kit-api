<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
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
        $this->middleware('throttle:60,1')->only(['calculateDelivery']);
    }

    public function calculateDelivery(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_city' => 'required|string',
            'to_city' => 'required|string',
            'weight' => 'required|numeric|min:0.1',
            'length' => 'required|numeric|min:1',
            'width' => 'required|numeric|min:1',
            'height' => 'required|numeric|min:1',
        ]);

        try {
            $result = $this->kitService->calculateDelivery($validated);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function searchCities(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $cacheKey = 'cities_search_' . md5($query);

        $cities = Cache::tags(['cities'])->remember($cacheKey, now()->addHours(24), function () use ($query) {
            return City::where('name', 'like', "%{$query}%")
                ->orWhere('region', 'like', "%{$query}%")
                ->take(20)
                ->get();
        });

        return response()->json($cities);
    }

    public function getCityTerminals(string $cityId): JsonResponse
    {
        $cacheKey = 'city_terminals_' . $cityId;

        $terminals = Cache::tags(['terminals'])->remember($cacheKey, now()->addHours(24), function () use ($cityId) {
            return Terminal::where('city_id', $cityId)
                ->with('city')
                ->get();
        });

        return response()->json($terminals);
    }

    public function searchTerminals(Request $request): JsonResponse
    {
        $query = $request->input('query');
        $cacheKey = 'terminals_search_' . md5($query);

        $terminals = Cache::tags(['terminals'])->remember($cacheKey, now()->addHours(24), function () use ($query) {
            return Terminal::where('name', 'like', "%{$query}%")
                ->orWhere('address', 'like', "%{$query}%")
                ->with('city')
                ->take(20)
                ->get();
        });

        return response()->json($terminals);
    }
}