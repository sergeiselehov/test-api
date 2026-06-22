<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $hasPagination = $this->list instanceof LengthAwarePaginator ||
            $isResource && ($this->list->resource instanceof AbstractPaginator || $this->list->resource instanceof AbstractCursorPaginator);

        $list = $isResource ? $this->list->resource : $this->list;

        return response()->json([
            'data' => method_exists($list, 'items') ? $list->items() : $list,
            'payload' => $this->payload,
            'pagination' => $hasPagination ? [
                'current_page' => $list->currentPage(),
                'last_page' => $list->lastPage(),
                'per_page' => $list->perPage(),
                'total' => $list->total(),
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
