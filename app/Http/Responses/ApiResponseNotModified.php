<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponseNotModified extends ApiResponse
{
    protected int $statusCode = self::CODE_NOT_MODIFIED;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json(null, self::CODE_NOT_MODIFIED, $this->getHeaders());
    }
}
