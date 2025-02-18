<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TerminalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TerminalController
{
    private TerminalRepository $repository;

    /**
     * @param TerminalRepository $repository
     */
    public function __construct(TerminalRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2'
        ]);

        try {
            $terminals = $this->repository->search(
                $validated['query'],
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
