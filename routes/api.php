<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth endpoints: register, login, logout
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Simple API resources for the demo (users, posts, comments)
Route::apiResource('users', UserController::class)->only(['index','show','store']);
Route::apiResource('posts', PostController::class)->only(['index','show','store']);
Route::apiResource('comments', CommentController::class)->only(['index','show','store']);
