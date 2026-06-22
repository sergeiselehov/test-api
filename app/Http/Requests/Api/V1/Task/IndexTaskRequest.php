<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Task;

use App\Http\Requests\Request;
use App\UseCases\Task\DTO\ListTasksDTO;

class IndexTaskRequest extends Request
{
    public function rules(): array
    {
        $sortable = implode('|', array_map('preg_quote', ListTasksDTO::SORTABLE_FIELDS));
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'regex:/^-?(' . $sortable . ')$/'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
        ];
    }
}
