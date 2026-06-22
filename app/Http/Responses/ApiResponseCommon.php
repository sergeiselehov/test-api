<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseCommon extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    protected array|Arrayable $data;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'data' => $this->data ?? [],
        ], $this->statusCode, $this->getHeaders());
    }

    /**
     * Set response data.
     */
    public function data(array|Arrayable $data): self
    {
        $this->data = $data;
        return $this;
    }
}
