<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'date'],
            'priority' => ['sometimes', 'string', Rule::in(TaskPriority::values())],
            'category' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'string', Rule::in(TaskStatus::values())],
        ];
    }
}
