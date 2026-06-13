<?php

use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/phones', PhoneController::class);

// Public: get phones by IDs (for guest favorites)
Route::post('/phones-by-ids', function (Request $request) {
    $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
    $phones = \App\Models\Phone::whereIn('id', $request->ids)->get();
    return response()->json(['phones' => $phones]);
});

// Public review routes
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{id}', [ReviewController::class, 'getIndexById']);

// Protected review routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});
