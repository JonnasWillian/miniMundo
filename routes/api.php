<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Projeto;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/projeto', [Projeto::class, 'view']);
Route::post('/projeto', [Projeto::class, 'create']);
Route::put('/projeto/{id}', [Projeto::class, 'update']);
Route::delete('/projeto/{id}', [Projeto::class, 'destroy']);