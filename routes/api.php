<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Route::post('/register', [UserController::class, 'register']);
// Route::post('/login', [UserController::class, 'login']);
// Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware('auth:api');
// Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);