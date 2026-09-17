<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::select('SELECT 1');
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'data' => ['service' => 'comi-api', 'status' => 'unavailable'],
            ], 503)->header('Cache-Control', 'no-store');
        }

        return response()->json([
            'data' => ['service' => 'comi-api', 'status' => 'ok'],
        ])->header('Cache-Control', 'no-store');
    }
}
