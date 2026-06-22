<?php

declare(strict_types=1);

namespace App\UseCases\Task;

use App\Models\Task;
use App\UseCases\Task\DTO\ListTasksDTO;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListTasksUseCase
{
    public function execute(ListTasksDTO $data): LengthAwarePaginator
    {
        return Task::query()
            ->when($data->search, fn ($query) => $query->where('title', 'like', '%' . $data->search . '%'))
            ->orderBy($data->sortField, $data->sortDirection)
            ->paginate(perPage: $data->perPage, page: $data->page);
    }
}
