<?php

declare(strict_types=1);

namespace App\UseCases\Task;

use App\Exceptions\TaskNotFoundException;

final readonly class DeleteTaskUseCase
{
    public function __construct(
        private ShowTaskUseCase $showTask,
    ) {
    }

    /**
     * @throws TaskNotFoundException
     */
    public function execute(int $taskId): void
    {
        $this->showTask->execute($taskId)->delete();
    }
}
