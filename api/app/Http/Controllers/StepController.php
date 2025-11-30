<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StepRequest;
use App\Http\Resources\StepResource;
use App\Models\Step;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StepController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);

        return response()->json([
            'data' => StepResource::collection(Step::with('patient')->latest()->paginate($perPage)),
        ], JsonResponse::HTTP_OK);
    }

    public function store(StepRequest $request): JsonResponse
    {
        return response()->json([
            'data' => new StepResource(Step::create($request->validated())),
        ], JsonResponse::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new StepResource(Step::with('patient')->findOrFail($id)),
        ], JsonResponse::HTTP_OK);
    }

    public function update(StepRequest $request, string $id): JsonResponse
    {
        Step::findOrFail($id)->update($request->validated());

        return response()->json([
            'data' => null,
        ], JsonResponse::HTTP_NO_CONTENT);
    }

    public function destroy(string $id): JsonResponse
    {
        Step::findOrFail($id)->delete();

        return response()->json([
            'data' => null,
        ], JsonResponse::HTTP_NO_CONTENT);
    }
}
