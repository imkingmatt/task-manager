<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request)
    {
        $tasks = $this->taskService->listAll($request->query('search'));
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('admin.tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->create($request->user(), $request->validated());
        return redirect()->route('admin.tasks.index')->with('status', 'Task created.');
    }

    public function edit(Task $task)
    {
        return view('admin.tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->taskService->update($task, $request->validated());
        return redirect()->route('admin.tasks.index')->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $this->taskService->delete($task);
        return redirect()->route('admin.tasks.index')->with('status', 'Task deleted.');
    }
}