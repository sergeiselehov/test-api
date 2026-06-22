<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Task\IndexTaskRequest;
use App\Http\Requests\Api\V1\Task\StoreTaskRequest;
use App\Http\Requests\Api\V1\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Responses\ApiResponse;
use App\UseCases\Task\CreateTaskUseCase;
use App\UseCases\Task\DTO\CreateTaskDTO;
use App\UseCases\Task\DTO\ListTasksDTO;
use App\UseCases\Task\DTO\UpdateTaskDTO;
use App\UseCases\Task\DeleteTaskUseCase;
use App\UseCases\Task\ListTasksUseCase;
use App\UseCases\Task\ShowTaskUseCase;
use App\UseCases\Task\UpdateTaskUseCase;

class TaskController extends Controller
{
    public function index(IndexTaskRequest $request, ListTasksUseCase $listTasks): ApiResponse
    {
        $tasks = $listTasks->execute(ListTasksDTO::fromArray($request->validated()));
        return ApiResponse::listPagination()->items(TaskResource::collection($tasks));
    }

    public function store(StoreTaskRequest $request, CreateTaskUseCase $createTask): ApiResponse
    {
        $task = $createTask->execute(CreateTaskDTO::fromArray($request->validated()));
        return ApiResponse::createdWithData(['id' => $task->id], 'Task created successfully');
    }

    public function show(int $id, ShowTaskUseCase $showTask): ApiResponse
    {
        $task = $showTask->execute($id);
        return ApiResponse::common(TaskResource::make($task)->toArray(request()));
    }

    public function update(UpdateTaskRequest $request, int $id, UpdateTaskUseCase $updateTask): ApiResponse
    {
        $updateTask->execute($id, UpdateTaskDTO::fromArray($request->validated()));
        return ApiResponse::success('Task updated successfully');
    }

    public function destroy(int $id, DeleteTaskUseCase $deleteTask): ApiResponse
    {
        $deleteTask->execute($id);
        return ApiResponse::success('Task deleted successfully');
    }
}
