<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Support\RecordsApiExamples;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;
    use RecordsApiExamples;

    public function test_can_create_task(): void
    {
        $payload = [
            'title' => 'Задача1',
            'description' => 'Задача1 описание',
            'due_date' => '2025-01-20T15:00:00',
            'priority' => TaskPriority::High->value,
            'category' => 'Работа',
            'status' => TaskStatus::Pending->value,
        ];

        $response = $this->postJson('/api/v1/tasks', $payload);
        $response->assertCreated()->assertJsonStructure([
            'code',
            'data' => ['id'],
            'message',
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Задача1',
            'category' => 'Работа',
            'priority' => TaskPriority::High->value,
            'status' => TaskStatus::Pending->value,
        ]);
    }

    public function test_create_task_requires_mandatory_fields(): void
    {
        $response = $this->postJson('/api/v1/tasks', []);
        $response->assertStatus(422)->assertJsonStructure([
            'code',
            'errors' => ['title', 'due_date', 'priority', 'category'],
            'message',
        ]);
    }

    public function test_create_task_rejects_invalid_priority(): void
    {
        $response = $this->postJson('/api/v1/tasks', [
            'title' => 'Задача1',
            'due_date' => '2025-01-20T15:00:00',
            'priority' => 'invalid-priority',
            'category' => 'Работа',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_list_tasks_with_pagination(): void
    {
        Task::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/tasks?per_page=5');
        $response->assertOk()->assertJsonCount(5, 'data');
        $response->assertJsonPath('pagination.per_page', 5);
        $response->assertJsonPath('pagination.total', 20);
    }

    public function test_can_search_tasks_by_title(): void
    {
        Task::factory()->create(['title' => 'Купить молоко']);
        Task::factory()->create(['title' => 'Сделать отчёт']);

        $response = $this->getJson('/api/v1/tasks?search=молоко');
        $response->assertOk()->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Купить молоко');
    }

    public function test_can_sort_tasks_by_due_date(): void
    {
        $earlier = Task::factory()->create(['due_date' => now()->addDay()]);
        $later = Task::factory()->create(['due_date' => now()->addWeek()]);

        $response = $this->getJson('/api/v1/tasks?sort=due_date');
        $response->assertOk();
        $ids = array_column($response->json('data'), 'id');

        $this->assertSame([$earlier->id, $later->id], $ids);
    }

    public function test_can_sort_tasks_by_due_date_descending(): void
    {
        $earlier = Task::factory()->create(['due_date' => now()->addDay()]);
        $later = Task::factory()->create(['due_date' => now()->addWeek()]);

        $response = $this->getJson('/api/v1/tasks?sort=-due_date');
        $response->assertOk();
        $ids = array_column($response->json('data'), 'id');

        $this->assertSame([$later->id, $earlier->id], $ids);
    }

    public function test_can_sort_tasks_by_created_at(): void
    {
        $earlier = Task::factory()->create();
        $later = Task::factory()->create();

        $response = $this->getJson('/api/v1/tasks?sort=created_at');
        $response->assertOk();
        $ids = array_column($response->json('data'), 'id');

        $this->assertSame([$earlier->id, $later->id], $ids);
    }

    public function test_can_sort_tasks_by_created_at_descending(): void
    {
        $earlier = Task::factory()->create();
        $later = Task::factory()->create();

        $response = $this->getJson('/api/v1/tasks?sort=-created_at');
        $response->assertOk();
        $ids = array_column($response->json('data'), 'id');

        $this->assertSame([$later->id, $earlier->id], $ids);
    }

    public function test_list_uses_default_pagination_when_per_page_is_not_set(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/tasks');
        $response->assertOk()->assertJsonCount(3, 'data');
        $response->assertJsonPath('pagination.current_page', 1);
    }

    public function test_can_show_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");
        $response->assertOk()->assertJsonPath('data.id', $task->id);
    }

    public function test_show_returns_404_for_missing_task(): void
    {
        $response = $this->getJson('/api/v1/tasks/999999');
        $response->assertStatus(404);
    }

    public function test_can_partially_update_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Задача2',
            'priority' => TaskPriority::High,
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Задача2 обновлена',
            'priority' => TaskPriority::Low->value,
            'status' => TaskStatus::Done->value,
        ]);

        $response->assertOk()->assertJson([
            'code' => 200,
            'message' => 'Task updated successfully',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Задача2 обновлена',
            'priority' => TaskPriority::Low->value,
            'status' => TaskStatus::Done->value,
            'category' => $task->category,
        ]);
    }

    public function test_can_partially_update_task_via_patch(): void
    {
        $task = Task::factory()->create([
            'title' => 'Задача3',
            'priority' => TaskPriority::High,
            'status' => TaskStatus::Pending,
        ]);

        $response = $this->patchJson("/api/v1/tasks/{$task->id}", [
            'status' => TaskStatus::Done->value,
        ]);

        $response->assertOk()->assertJson([
            'code' => 200,
            'message' => 'Task updated successfully',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Задача3',
            'status' => TaskStatus::Done->value,
        ]);
    }

    public function test_can_update_category_and_description(): void
    {
        $task = Task::factory()->create([
            'category' => 'Работа',
            'description' => 'Старое описание',
        ]);

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'category' => 'Личное',
            'description' => 'Новое описание',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'category' => 'Личное',
            'description' => 'Новое описание',
        ]);
    }

    public function test_update_rejects_invalid_priority(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'priority' => 'invalid-priority',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_rejects_invalid_status(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'status' => 'invalid-status',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_rejects_invalid_due_date(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson("/api/v1/tasks/{$task->id}", [
            'due_date' => 'not-a-date',
        ]);

        $response->assertStatus(422);
    }

    public function test_patch_rejects_invalid_status(): void
    {
        $task = Task::factory()->create();

        $response = $this->patchJson("/api/v1/tasks/{$task->id}", [
            'status' => 'invalid-status',
        ]);

        $response->assertStatus(422);
    }

    public function test_patch_returns_404_for_missing_task(): void
    {
        $response = $this->patchJson('/api/v1/tasks/999999', ['title' => 'X']);
        $response->assertStatus(404);
    }

    public function test_update_returns_404_for_missing_task(): void
    {
        $response = $this->putJson('/api/v1/tasks/999999', ['title' => 'X']);
        $response->assertStatus(404);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");
        $response->assertOk()->assertJson([
            'code' => 200,
            'message' => 'Task deleted successfully',
        ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_delete_returns_404_for_missing_task(): void
    {
        $response = $this->deleteJson('/api/v1/tasks/999999');
        $response->assertStatus(404);
    }
}
