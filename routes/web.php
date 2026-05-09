<?php

use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ProfileController;
use App\Models\Phone;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::inertia('/', 'Home')->name('home');

// Phone detail
Route::get('/phones/{id}', function ($id) {
    $phone = Phone::with('reviews')->findOrFail($id);
    return Inertia::render('PhoneDetail', ['phone' => $phone]);
});

// Compare
Route::get('/compare', function () {
    $phones = Phone::all();
    return Inertia::render('Compare', ['phones' => $phones]);
})->name('compare');

// Dashboard
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
