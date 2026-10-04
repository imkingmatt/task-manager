<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    public function listOwnedBy(User $user, ?string $search = null): LengthAwarePaginator
    {
        $query = $user->tasks();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->latest()->paginate(10);
    }

    public function listAll(?string $search = null): LengthAwarePaginator
    {
        $query = Task::query()->with('user');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->latest()->paginate(10);
    }

    public function create(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function toggleComplete(Task $task): Task
    {
        $task->update([
            'status' => $task->status === 'completed' ? 'pending' : 'completed',
        ]);
        return $task;
    }
}