<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Rolecontroller;
use App\Http\Controllers\UserRolecontroller;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('user-roles')->group(function (){
    Route::get('/', [UserRolecontroller::class, 'index']);
    Route::post('/assign', [UserRolecontroller::class, 'assignRole']);
    Route::delete('/remove/{id}', [UserRolecontroller::class, 'removeRole']);
    Route::get('/{userId}', [UserRolecontroller::class, 'getUserRoles']);
});

Route::post('auth/login', [AuthController::class, 'login']);
Route::apiResource('roles', Rolecontroller::class);
Route::apiResource('auth', AuthController::class);
Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
});
