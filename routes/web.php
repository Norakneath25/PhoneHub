<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ProfileController;
use App\Models\Phone;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', function () {
    $phones = Phone::all();

    return Inertia::render('Home', ['phones' => $phones]);
})->name('home');

// Phone detail
Route::get('/phones/{id}', function (string $id) {
    $phone = Phone::with('reviews.user')->findOrFail($id);

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
// Blog

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reviews
    Route::post('/reviews', function (Request $request) {
        $request->validate([
            'phone_id' => 'required|exists:phones,id',
            'comment' => 'required|string|max:1000',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $review = Review::create([
            'phone_id' => $request->phone_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ]);
    });
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/phones/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/phones', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/phones/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/phones/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/phones/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::post('/phones/bulk-delete', [PhoneController::class, 'bulkDelete'])->name('admin.bulk-delete');
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::delete('/reviews/{id}', [AdminController::class, 'destroyReview'])->name('admin.reviews.destroy');

    // scrape
    Route::post('/bulk-scrape', [AdminController::class, 'bulkScrape'])->name('admin.bulk-scrape');
    Route::post('/scrape-links', [AdminController::class, 'scrapeLinks'])->name('admin.scrape-links');
    Route::post('/scrape-product', [AdminController::class, 'scrapeProduct'])->name('admin.scrape-product');
});

require __DIR__.'/auth.php';
