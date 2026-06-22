<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class ApiResponseList extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    protected array|Arrayable|Collection|AnonymousResourceCollection $list;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        $isResource = $this->list instanceof ResourceCollection;
        $list = $isResource ? $this->list->resource : $this->list;

        return response()->json([
            'data' => method_exists($list, 'items') ? $list->items() : $list,
            'payload' => $this->payload ?? [],
        ], $this->statusCode, $this->getHeaders());
    }

    /**
     * List items.
     */
    public function items(array|Collection|Arrayable|AnonymousResourceCollection $list): ApiResponseList
    {
        $this->list = $list;
        return $this;
    }
}
