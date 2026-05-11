<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $phones = Phone::all();

        return Inertia::render('Admin/Index', ['phones' => $phones]);
    }

    public function create()
    {
        return Inertia::render('Admin/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'store_url' => 'required|string',
            'ram' => 'required|string',
            'storage' => 'required|string',
            'camera' => 'required|string',
            'battery' => 'required|string',
            'screen_size' => 'required|string',
            'os' => 'required|string',
            'release_date' => 'required|date',
            'rating' => 'required|numeric',
        ]);

        Phone::create($request->all());

        return redirect()->route('admin.index');
    }

    public function edit(string $id)
    {
        $phone = Phone::findOrFail($id);

        return Inertia::render('Admin/Edit', ['phone' => $phone]);
    }

    public function update(Request $request, string $id)
    {
        $phone = Phone::findOrFail($id);
        $phone->update($request->all());

        return redirect()->route('admin.index');
    }

    public function destroy(string $id)
    {
        $phone = Phone::findOrFail($id);
        $phone->delete();

        return redirect()->route('admin.index');
    }

    public function reviews()
    {
        $reviews = Review::with(['phone', 'user'])->get();

        return Inertia::render('Admin/Reviews', ['reviews' => $reviews]);
    }

    public function destroyReview(string $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews');
    }
}
