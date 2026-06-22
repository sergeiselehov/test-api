<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class TaskNotFoundException extends RuntimeException
{
    public function __construct(int $taskId)
    {
        parent::__construct("Задача с ID {$taskId} не найдена.");
    }
}
