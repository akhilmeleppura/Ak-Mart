<?php

namespace App\Helpers\HS;

use Illuminate\Http\JsonResponse;

class Reply
{
    /**
     * Return a successful response with data
     */
    public static function successWithData(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return a successful response without data
     */
    public static function success(string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Return an error response
     */
    public static function error(string $message, array $errors = [], int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Return a validation error response
     */
    public static function validationError(string $message, array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }

    /**
     * Return a not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 404);
    }

    /**
     * Return an unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }
}
