<?php

namespace App\Http\Controllers;

use App\Services\AdDataService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct(
        protected AdDataService $adDataService
    ) {}

    /**
     * Display the dashboard with aggregated campaign metrics.
     */
    public function __invoke(): Response
    {
        $result = $this->adDataService->getAggregatedMetrics();
        Log::warning('Ad API URL or token not configured');
        return Inertia::render('Dashboard', [
            'metrics' => $result['metrics'],
            'error' => $result['error'] ?? null,
        ]);
    }
}
