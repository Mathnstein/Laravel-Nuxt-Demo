<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     * Return a list of all tasks.
     */
    public function index(): AnonymousResourceCollection
    {
        return TaskResource::collection(Task::all());
    }

    /**
     * POST /api/tasks
     * Store a newly created task in storage.
     */
    public function store(Request $request): TaskResource
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'completed' => 'boolean',
        ]);
        $task = Task::create($validated);
        return new TaskResource($task);
    }

    /**
    * PATCH /api/tasks/{task}
    * Update the specified task in storage.
    */
    public function update(Request $request, Task $task): TaskResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'completed' => 'sometimes|boolean',
        ]);
        $task->update($validated);
        return new TaskResource($task);
    }

    /**
     * DELETE /api/tasks/{task}
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();
        // Return a 204 No Content response to indicate successful deletion
        return response()->noContent();
    }
}
