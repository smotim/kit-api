<?php

namespace App\Http\Controllers;

use App\Repositories\TerminalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    private TerminalRepository $repository;

    public function __construct(TerminalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'city_id' => 'nullable|string'
        ]);

        try {
            $terminals = $this->repository->search(
                $validated['query'],
                $validated['city_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $terminals
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
