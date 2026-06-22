<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'string', Rule::in(TaskPriority::values())],
            'category' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(TaskStatus::values())],
        ];
    }
}
