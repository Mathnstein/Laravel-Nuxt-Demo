<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
/**
     * Handle the Task "saved" event (Covers Create and Update).
     */
    public function saved(Task $task): void
    {
        Cache::forget(Task::cacheKey($task->user_id));
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        Cache::forget(Task::cacheKey($task->user_id));
    }

    /**
     * Handle the Task "restored" event (If using Soft Deletes).
     */
    public function restored(Task $task): void
    {
        Cache::forget(Task::cacheKey($task->user_id));
    }
}
