<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['name', 'completed'];
    protected $casts = [
        'completed' => 'boolean',
        'id' => 'integer'
    ];

    /**
     * The Single Source of Truth for the Task Cache Key.
     */
    public static function cacheKey(?int $userId = null): string
    {
        $userId = $userId ?? 'guest';
        return "tasks:all:user:{$userId}";
    }
}
