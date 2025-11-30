<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json([
            'data' => $data,
        ]);
    }
}
