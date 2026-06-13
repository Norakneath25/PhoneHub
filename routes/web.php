<?php

use App\Http\Controllers\AdminBlogController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PhoneController;
use App\Http\Controllers\ProfileController;
use App\Models\Phone;
use App\Models\PhoneStorePrice;
use App\Models\Review;
use App\Models\SavedComparison;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', [PhoneController::class, 'search'])->name('home');

// Phone detail
Route::get('/phones/{id}', function (string $id) {
    $phone = Phone::with('reviews.user')->findOrFail($id);
    $storePrices = PhoneStorePrice::with('store')
        ->where('phone_id', $id)
        ->where('in_stock', true)
        ->orderBy('price', 'asc')
        ->get();

    return Inertia::render('PhoneDetail', [
        'phone' => $phone,
        'storePrices' => $storePrices,
    ]);
});

// Compare
Route::get('/compare', function () {
    $phones = Phone::all();

    return Inertia::render('Compare', ['phones' => $phones]);
})->name('compare');

// Dashboard
Route::get('/dashboard', function () {
    $favorites = [];
    $comparisons = [];
    if (auth()->check()) {
        $favorites = auth()->user()->favorites()->latest()->take(8)->get();
        $comparisons = auth()->user()->savedComparisons()->latest()->get();
    }

    return Inertia::render('Dashboard', [
        'favorites' => $favorites,
        'savedComparisons' => $comparisons,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Upcoming phones
Route::get('/upcoming', function () {
    $upcoming = Phone::where('release_date', '>', now())
        ->orWhereNull('release_date')
        ->orderBy('release_date', 'asc')
        ->get();

    return Inertia::render('Upcoming', ['phones' => $upcoming]);
})->name('upcoming');

// All Products
Route::get('/products', [PhoneController::class, 'allProducts'])->name('products');

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

    // Favorites
    Route::post('/favorites/{phoneId}', function (int $phoneId) {
        $phone = Phone::findOrFail($phoneId);
        auth()->user()->favorites()->toggle($phoneId);

        return back()->with('success', 'Favorite updated');
    })->name('favorites.toggle');

    Route::get('/favorites', function () {
        $favorites = auth()->user()->favorites()->latest()->get();

        return Inertia::render('Favorites', ['phones' => $favorites]);
    })->name('favorites.index');

    // Saved Comparisons
    Route::post('/saved-comparisons', function (Request $request) {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone_ids' => 'required|array|min:2|max:3',
            'phone_ids.*' => 'exists:phones,id',
        ]);

        auth()->user()->savedComparisons()->create([
            'name' => $request->name ?? 'Comparison ' . now()->format('M d, Y'),
            'phone_ids' => $request->phone_ids,
        ]);

        return back()->with('success', 'Comparison saved!');
    })->name('saved-comparisons.store');

    Route::delete('/saved-comparisons/{id}', function (int $id) {
        $comparison = SavedComparison::where('user_id', auth()->id())->findOrFail($id);
        $comparison->delete();

        return back()->with('success', 'Comparison deleted');
    })->name('saved-comparisons.destroy');
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

    // Blog posts
    Route::get('/blog', [AdminBlogController::class, 'index'])->name('admin.blog.index');
    Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('admin.blog.create');
    Route::post('/blog', [AdminBlogController::class, 'store'])->name('admin.blog.store');
    Route::get('/blog/{post}/edit', [AdminBlogController::class, 'edit'])->name('admin.blog.edit');
    Route::put('/blog/{post}', [AdminBlogController::class, 'update'])->name('admin.blog.update');
    Route::delete('/blog/{post}', [AdminBlogController::class, 'destroy'])->name('admin.blog.destroy');

    // scrape
    Route::post('/bulk-scrape', [AdminController::class, 'bulkScrape'])->name('admin.bulk-scrape');
    Route::post('/scrape-links', [AdminController::class, 'scrapeLinks'])->name('admin.scrape-links');
    Route::post('/scrape-product', [AdminController::class, 'scrapeProduct'])->name('admin.scrape-product');

    // Store management
    Route::get('/stores', function () {
        $stores = Store::withCount('phonePrices')->get();

        return Inertia::render('Admin/Stores/Index', ['stores' => $stores]);
    })->name('admin.stores.index');

    Route::post('/stores', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'required|url',
            'logo_url' => 'nullable|url',
        ]);

        Store::create($request->all());

        return redirect()->route('admin.stores.index')->with('success', 'Store added');
    })->name('admin.stores.store');

    Route::delete('/stores/{id}', function (int $id) {
        Store::findOrFail($id)->delete();

        return back()->with('success', 'Store deleted');
    })->name('admin.stores.destroy');

    // Store Prices for phones
    Route::post('/phones/{phoneId}/prices', function (Request $request, int $phoneId) {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'price' => 'required|numeric|min:0',
            'product_url' => 'nullable|url',
            'in_stock' => 'boolean',
        ]);

        PhoneStorePrice::updateOrCreate(
            ['phone_id' => $phoneId, 'store_id' => $request->store_id],
            [
                'price' => $request->price,
                'product_url' => $request->product_url,
                'in_stock' => $request->in_stock ?? true,
            ]
        );

        return back()->with('success', 'Price updated');
    })->name('admin.phones.prices.store');

    Route::delete('/phones/prices/{priceId}', function (int $priceId) {
        PhoneStorePrice::findOrFail($priceId)->delete();

        return back()->with('success', 'Price deleted');
    })->name('admin.phones.prices.destroy');
});

require __DIR__.'/auth.php';
