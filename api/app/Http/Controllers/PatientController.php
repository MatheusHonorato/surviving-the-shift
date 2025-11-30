<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);

        $test = Test::where('enabled', true)->first();

        if (! $test) {
            return response()->json([
                'message' => 'no test enabled',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => PatientResource::collection(Patient::whereIn('id', $test->patients->pluck('id'))->with('steps')->inRandomOrder()->paginate($perPage)),
        ], JsonResponse::HTTP_OK);
    }

    public function store(PatientRequest $request): JsonResponse
    {
        return response()->json([
            'data' => new PatientResource(Patient::create($request->validated())),
        ], JsonResponse::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new PatientResource(Patient::with('steps')->findOrFail($id)),
        ], JsonResponse::HTTP_OK);
    }

    public function update(PatientRequest $request, string $id): JsonResponse
    {
        Patient::findOrFail($id)->update($request->validated());

        return response()->json([
            'data' => null,
        ], JsonResponse::HTTP_NO_CONTENT);
    }

    public function destroy(string $id): JsonResponse
    {
        Patient::findOrFail($id)->delete();

        return response()->json([
            'data' => null,
        ], JsonResponse::HTTP_NO_CONTENT);
    }
}
