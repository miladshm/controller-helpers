<?php


\Illuminate\Support\Facades\Route::apiResource('testing', \Miladshm\ControllerHelpers\Http\Controllers\TestController::class);
\Illuminate\Support\Facades\Route::post('change-position/{id}', [\Miladshm\ControllerHelpers\Http\Controllers\TestController::class, 'changePosition']);
\Illuminate\Support\Facades\Route::post('change-status/{id}', [\Miladshm\ControllerHelpers\Http\Controllers\TestController::class, 'changeStatus']);