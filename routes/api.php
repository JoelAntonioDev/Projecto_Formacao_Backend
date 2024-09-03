<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::post('/add-user', [UserController::class, 'add']);
Route::delete('/delete-user/{id}', [UserController::class, 'deleteById']);
Route::put('/update-user/{id}', [UserController::class, 'update']);
Route::get('/get-users', [UserController::class, 'findAll']);
Route::get('/get-user/{id}', [UserController::class,'findById']);

