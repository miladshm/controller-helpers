<?php


use Illuminate\Support\Facades\Route;
use Miladshm\ControllerHelpers\Http\Controllers\TestController;

Route::apiResource('testing', TestController::class);
Route::post('change-position/{id}', [TestController::class, 'changePosition']);
Route::post('change-status/{id}', [TestController::class, 'changeStatus']);
Route::get('count/{group_by?}', [TestController::class, 'getCount']);
Route::get('sum/{column}', [TestController::class, 'getSum']);
