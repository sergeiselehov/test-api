<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;

class ApiResponseListPagination extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    protected array|Arrayable|Collection|LengthAwarePaginator|ResourceCollection $list;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        $isResource = $this->list instanceof ResourceCollection;
        $paginator = $isResource ? $this->list->resource : $this->list;
        $hasPagination = $paginator instanceof AbstractPaginator || $paginator instanceof AbstractCursorPaginator;
        $data = $isResource ? $this->list->toArray($request) : (method_exists($paginator, 'items') ? $paginator->items() : $paginator);

        return response()->json([
            'data' => $data,
            'payload' => $this->payload,
            'pagination' => $hasPagination ? [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ] : [],
        ], $this->statusCode, $this->getHeaders());
    }

    /**
     * List items.
     */
    public function items(array|Collection|Arrayable|LengthAwarePaginator|ResourceCollection $list): ApiResponseListPagination
    {
        $this->list = $list;
        return $this;
    }
}
