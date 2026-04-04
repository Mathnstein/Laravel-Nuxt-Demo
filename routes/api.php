<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

use App\Http\Resources\SystemStatusResource;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/status', function () {
    $redisStatus = 'Offline';

    try {
        // We attempt to get the connection and ping it
        // If the service is down, this will throw an exception immediately
        $redisStatus = Redis::connection()->ping() ? 'Online' : 'Offline';
    } catch (\Exception $e) {
        // Log the error if you want to see why it failed in storage/logs/laravel.log
        Log::warning('Redis Status Check Failed: ' . $e->getMessage());
        $redisStatus = 'Offline';
    }
    $data = [
        'title'            => 'Laravel + Nuxt Lab is Live!',
        'database_status'  => 'Connected',
        'database_version' => DB::select('select version() as version')[0]->version,
        'redis_status'     => $redisStatus,
        'server_time'      => now()->format('H:i:s'),
        'php_version'      => PHP_VERSION,
    ];

    return new SystemStatusResource($data);
});