<?php

use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('/phones',PhoneController::class);

Route::get('/reviews/',[ReviewController::class,'index']);
Route::get('/reviews/{id}',[ReviewController::class,'getIndexById']);
Route::post('/reviews/',[ReviewController::class,'store']);
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
