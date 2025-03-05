<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\TerminalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class TerminalController
{
    public function __construct(
        private TerminalRepository $repository
    ) {}

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
            $terminals = $this->repository->search($validated['query']);
            return response()->json([
                'data' => $terminals
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}