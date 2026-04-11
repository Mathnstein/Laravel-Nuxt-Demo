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
    public function index(Request $request): AnonymousResourceCollection
    {

        # Validate the search query parameter if provided
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
        ]);
        $search = $validated['search'] ?? null;
        $userId = auth()->id();
        $cacheKey = Task::cacheKey($userId);

        # If a search query is provided, bypass the cache and return filtered results
        if ($search) {
            $tasks = Task::where('name', 'like', "%{$search}%")->get();
            $fromCache = false; 
        } else {
            # Use redis cache to store the tasks
            $fromCache = cache()->has($cacheKey);
            $tasksData = cache()->remember($cacheKey, now()->addDay(), function () {
                return Task::all()->toArray();
            });

            # Hydrate the tasks data back into Eloquent models so we can use TaskResource
            $tasks = Task::hydrate($tasksData);
        }

        return TaskResource::collection($tasks)->additional([
            'meta' => [
                'cached' => $fromCache,
                'cache_key' => $cacheKey,
            ]
        ]);
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
