<?php

namespace App\Http\Controllers;

use App\Services\DatabaseSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class DatabaseSyncController extends Controller
{
    public function sync(DatabaseSyncService $syncService): JsonResponse
    {
        Log::info('Database sync initiated', [
            'user_id' => auth()->id(),
            'timestamp' => now(),
            'ip' => request()->ip()
        ]);

        try {
            // Track start time for performance logging
            $startTime = microtime(true);
            
            // Attempt sync
            $result = $syncService->sync();
            
            // Calculate execution time
            $executionTime = microtime(true) - $startTime;
            
            // Log success
            Log::info('Database sync completed successfully', [
                'execution_time' => round($executionTime, 2) . ' seconds',
                'user_id' => auth()->id(),
                'timestamp' => now(),
                'result' => $result ?? 'No return value',
                'memory_usage' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
            ]);

            return response()->json([
                'message' => 'Database sync completed successfully',
                'execution_time' => round($executionTime, 2) . ' seconds'
            ]);

        } catch (Throwable $e) {
            // Log detailed error information
            Log::error('Database sync failed', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'timestamp' => now(),
                'memory_usage' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
            ]);

            // Return more detailed error message
            return response()->json([
                'error' => 'Sync failed: ' . $e->getMessage(),
                'details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : 'Enable debug mode for more details'
            ], 500);
        }
    }
}