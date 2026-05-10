<?php
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Models\Phone;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminController;

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

// update phone
Route::get('/', function () {
    $phones = Phone::all();
    return Inertia::render('Home', ['phones' => $phones]);
})->name('home');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/phones/{id}', function ($id) {
    $phone = Phone::with('reviews')->findOrFail($id);
    return Inertia::render('PhoneDetail', [
        'phone' => $phone,
        'auth' => Auth::user(),
    ]);
});

// admin control
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/phones/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/phones', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/phones/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/phones/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/phones/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
});

require __DIR__.'/auth.php';
