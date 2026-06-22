<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('tasks/{id}', [TaskController::class, 'update'])->name('tasks.patch');
    Route::delete('tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
});
