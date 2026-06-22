<?php

declare(strict_types=1);

namespace App\UseCases\Task\DTO;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Support\Carbon;

final readonly class CreateTaskDTO
{
    public function __construct(
        public string $title,
        public ?string $description,
        public Carbon $dueDate,
        public TaskPriority $priority,
        public string $category,
        public TaskStatus $status,
    ) {
    }

    public static function fromArray(array $validated): self
    {
        return new self(
            title: $validated['title'],
            description: $validated['description'] ?? null,
            dueDate: Carbon::parse($validated['due_date']),
            priority: TaskPriority::from($validated['priority']),
            category: $validated['category'],
            status: isset($validated['status']) ? TaskStatus::from($validated['status']) : TaskStatus::Pending,
        );
    }

    public function toAttributes(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'priority' => $this->priority,
            'category' => $this->category,
            'status' => $this->status,
        ];
    }
}
