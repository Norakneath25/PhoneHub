<?php

use App\Models\Phone;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/compare', 'Compare')->name('compare');


Route::get('/phones/{id}', function ($id) {
    $phone = Phone::with('reviews')->findOrFail($id);
    return Inertia::render('PhoneDetail', ['phone' => $phone]);
});


Route::get('/compare', function () {
    $phones = Phone::all();
    return Inertia::render('Compare', ['phones' => $phones]);
})->name('compare');
