<?php

declare(strict_types=1);

namespace App\UseCases\Task;

use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class ShowTaskUseCase
{
    /**
     * @throws TaskNotFoundException
     */
    public function execute(int $taskId): Task
    {
        try {
            return Task::query()->findOrFail($taskId);
        } catch (ModelNotFoundException) {
            throw new TaskNotFoundException($taskId);
        }
    }
}
