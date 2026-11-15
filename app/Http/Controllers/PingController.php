<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PingController extends Controller
{
    /**
     * Health check endpoint verifying application and database connectivity.
     */
    public function __invoke()
    {
        $startTime = microtime(true);

        try {
            DB::connection()->getPdo();
            $dbLatencyMs = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => 'healthy',
                'message' => 'Jenkins auto deploy test',
                'timestamp' => now()->toIso8601String(),
                'services' => [
                    'app' => 'up',
                    'database' => [
                        'status' => 'connected',
                        'driver' => DB::connection()->getDriverName(),
                        'database' => DB::connection()->getDatabaseName(),
                        'latency_ms' => $dbLatencyMs,
                    ],
                ],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toIso8601String(),
                'services' => [
                    'app' => 'up',
                    'database' => [
                        'status' => 'disconnected',
                        'error' => $e->getMessage(),
                    ],
                ],
            ], 503);
        }
    }
}
