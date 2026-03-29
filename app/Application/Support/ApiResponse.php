<?php

namespace App\Application\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(mixed $data, array $meta = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => $meta,
            'error' => null,
        ], $status);
    }

    public static function error(string $message, int $status, ?string $code = null, array $details = []): JsonResponse
    {
        return response()->json([
            'data' => null,
            'meta' => [],
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }
}
