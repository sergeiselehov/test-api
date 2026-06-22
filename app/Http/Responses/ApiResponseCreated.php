<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponseCreated extends ApiResponse
{
    protected int $statusCode = self::CODE_CREATED;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'code' => $this->statusCode,
        ], $this->statusCode, $this->getHeaders());
    }
}
