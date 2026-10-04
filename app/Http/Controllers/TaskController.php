<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(Request $request)
    {
        $tasks = $this->taskService->listForUser($request->user(), $request->query('search'));
        return view('tasks.index', compact('tasks'));
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->create($request->user(), $request->validated());
        return redirect()->route('tasks.index')->with('status', 'Task created.');
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $this->taskService->update($task, $request->validated());
        return redirect()->route('tasks.index')->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskService->delete($task);
        return redirect()->route('tasks.index')->with('status', 'Task deleted.');
    }

    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        $this->taskService->toggleComplete($task);
        return back();
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }
}
