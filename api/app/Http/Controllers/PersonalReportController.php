<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PersonalReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalReportController extends Controller
{
    public function __construct(
        private readonly PersonalReportService $personalReportService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $data = $this->personalReportService->getReportData($userId);

        return response()->json([
            'data' => $data,
        ]);
    }
}
