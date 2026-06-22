<?php

declare(strict_types=1);

namespace App\UseCases\Task\DTO;

final readonly class ListTasksDTO
{
    public const array SORTABLE_FIELDS = ['due_date', 'created_at'];

    public function __construct(
        public ?string $search = null,
        public string $sortField = 'created_at',
        public string $sortDirection = 'desc',
        public int $perPage = 15,
        public int $page = 1,
    ) {
    }

    public static function fromArray(array $validated): self
    {
        [$field, $direction] = self::parseSort($validated['sort'] ?? null);
        return new self(
            search: $validated['search'] ?? null,
            sortField: $field,
            sortDirection: $direction,
            perPage: (int) ($validated['per_page'] ?? 15),
            page: (int) ($validated['page'] ?? 1),
        );
    }

    private static function parseSort(?string $sort): array
    {
        if ($sort === null || $sort === '') {
            return ['created_at', 'desc'];
        }

        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }

        if (!in_array($sort, self::SORTABLE_FIELDS, true)) {
            $sort = 'created_at';
        }

        return [$sort, $direction];
    }
}
