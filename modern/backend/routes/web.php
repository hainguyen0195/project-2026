<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['service' => 'comi-api', 'health' => '/api/v1/health']);
});
