<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->dashboardService->getDashboardData();

        return response()->json(new DashboardResource($data));
    }

    public function indexWeb(Request $request)
    {
        $data = $this->dashboardService->getDashboardData();

        return view('app.dashboard', $data);
    }
}