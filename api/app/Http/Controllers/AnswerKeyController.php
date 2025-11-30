<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AnswerKeyRequest;
use App\Http\Resources\AnswerKeyResource;
use App\Models\AnswerKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnswerKeyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $stepId = $request->query('step_id');

        $query = AnswerKey::latest();

        if ($stepId) {
            $query->where('step_id', $stepId);
        }

        return response()->json([
            'data' => AnswerKeyResource::collection($query->paginate($perPage)),
        ], JsonResponse::HTTP_OK);
    }

    public function store(AnswerKeyRequest $request): JsonResponse
    {
        return response()->json([
            'data' => new AnswerKeyResource(AnswerKey::create($request->validated())),
        ], JsonResponse::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new AnswerKeyResource(AnswerKey::findOrFail($id)),
        ], JsonResponse::HTTP_OK);
    }

    public function update(AnswerKeyRequest $request, string $id): JsonResponse
    {
        AnswerKey::findOrFail($id)->update($request->validated());

        return response()->json([
            'data' => null,
        ], JsonResponse::HTTP_NO_CONTENT);
    }

    public function destroy(string $id): JsonResponse
    {
        AnswerKey::findOrFail($id)->delete();

        return response()->json([
            'data' => null,
        ], JsonResponse::HTTP_NO_CONTENT);
    }
}
