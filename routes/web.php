<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    $path = public_path('index.html');
    if (!File::exists($path)) {
        return response()->json(['message' => 'Frontend not built, but brain active'], 200);
    }
    return File::get($path);
});
