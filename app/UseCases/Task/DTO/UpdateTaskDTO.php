<?php

declare(strict_types=1);

namespace App\UseCases\Task\DTO;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Support\Carbon;

final readonly class UpdateTaskDTO
{
    private function __construct(
        public array $attributes,
    ) {
    }

    public static function fromArray(array $validated): self
    {
        $attributes = [];

        if (array_key_exists('title', $validated)) {
            $attributes['title'] = $validated['title'];
        }

        if (array_key_exists('description', $validated)) {
            $attributes['description'] = $validated['description'];
        }

        if (array_key_exists('due_date', $validated)) {
            $attributes['due_date'] = Carbon::parse($validated['due_date']);
        }

        if (array_key_exists('priority', $validated)) {
            $attributes['priority'] = TaskPriority::from($validated['priority']);
        }

        if (array_key_exists('category', $validated)) {
            $attributes['category'] = $validated['category'];
        }

        if (array_key_exists('status', $validated)) {
            $attributes['status'] = TaskStatus::from($validated['status']);
        }

        return new self($attributes);
    }

    public function isEmpty(): bool
    {
        return $this->attributes === [];
    }
}
