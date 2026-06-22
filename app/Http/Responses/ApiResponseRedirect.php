<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponseRedirect extends ApiResponse
{
    protected int $statusCode = self::CODE_REDIRECT;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'code' => $this->statusCode,
            'message' => $this->message ?? null,
        ], $this->statusCode, $this->getHeaders());
    }
}
