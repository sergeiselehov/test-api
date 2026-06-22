<?php

declare(strict_types=1);

namespace App\UseCases\Task;

use App\Exceptions\TaskNotFoundException;
use App\Models\Task;
use App\UseCases\Task\DTO\UpdateTaskDTO;

final readonly class UpdateTaskUseCase
{
    public function __construct(
        private ShowTaskUseCase $showTask,
    ) {
    }

    /**
     * @throws TaskNotFoundException
     */
    public function execute(int $taskId, UpdateTaskDTO $data): Task
    {
        $task = $this->showTask->execute($taskId);

        if (!$data->isEmpty()) {
            $task->update($data->attributes);
        }

        return $task->refresh();
    }
}
