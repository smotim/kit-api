<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TerminalSearchRequest;
use App\Repositories\TerminalRepository;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

readonly class TerminalController
{
    public function __construct(
        private TerminalRepository $repository
    ) {
    }

    /**
     * @param TerminalSearchRequest $request
     * @return JsonResponse
     */
    public function search(TerminalSearchRequest $request): JsonResponse
    {
        try {
            $terminals = $this->repository->search($request->validated('query'));
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
