<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;

class ApiResponseCreatedWithData extends ApiResponse
{
    protected int $statusCode = self::CODE_CREATED;

    /** @var array|Arrayable */
    protected $data;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        $data = $this->data instanceof Arrayable ? $this->data->toArray() : ($this->data ?? []);

        return response()->json([
            'code' => $this->statusCode,
            'data' => $data,
            'message' => $this->message ?? null,
        ], $this->statusCode, $this->getHeaders());
    }

    /**
     * Set response data.
     *
     * @param array|Arrayable $data
     */
    public function data($data): self
    {
        $this->data = $data;
        return $this;
    }
}