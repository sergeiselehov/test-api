<?php

declare(strict_types=1);

namespace App\UseCases\Task;

use App\Models\Task;
use App\UseCases\Task\DTO\CreateTaskDTO;

final class CreateTaskUseCase
{
    public function execute(CreateTaskDTO $data): Task
    {
        return Task::query()->create($data->toAttributes());
    }
}
