<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AnswerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function __construct(
        private readonly AnswerService $answerService
    ) {
    }

    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'step_id' => ['required', 'integer', 'exists:steps,id'],
        ]);

        $userId = auth()->id();
        $stepId = $validated['step_id'];

        $this->answerService->startAnswer($userId, $stepId);

        return response()->json([], JsonResponse::HTTP_CREATED);
    }

    public function finish(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'step_id' => ['required', 'integer', 'exists:steps,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:alternatives,id'],
        ]);

        $userId = auth()->id();
        $stepId = $validated['step_id'];
        $alternativeId = $validated['alternative_id'] ?? null;

        $this->answerService->finishAnswer($userId, $stepId, $alternativeId);

        return response()->json([], JsonResponse::HTTP_CREATED);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'step_id' => ['required', 'integer', 'exists:steps,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:alternatives,id'],
        ]);

        $userId = auth()->id();
        $stepId = $validated['step_id'];
        $alternativeId = $validated['alternative_id'] ?? null;

        $this->answerService->storeAnswer($userId, $stepId, $alternativeId);

        return response()->json([], JsonResponse::HTTP_CREATED);
    }
}
