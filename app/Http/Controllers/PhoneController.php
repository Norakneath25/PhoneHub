<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $phones = Phone::all();

        return response()->json([
            'status' => 'success',
            'data' => $phones,
        ], 200);
    }

    /**
     * Search and filter phones (backend filtering)
     */
    public function search(Request $request)
    {
        // Home page: return separate curated lists
        $recommended = Phone::orderBy('rating', 'desc')->take(6)->get();
        $latest = Phone::orderBy('release_date', 'desc')->take(6)->get();
        $budget = Phone::where('price', '<', 500)->orderBy('price', 'asc')->take(6)->get();

        // If user searches or filters, show matching results
        if ($request->filled('q') || $request->filled('brand') || $request->filled('ram') || $request->filled('storage') || $request->filled('sort')) {
            $query = Phone::query();

            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('brand', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%");
                });
            }

            if ($request->filled('brand') && $request->brand !== 'All') {
                $query->where('brand', $request->brand);
            }

            if ($request->filled('ram') && $request->ram !== 'All') {
                $query->where('ram', $request->ram);
            }

            if ($request->filled('storage') && $request->storage !== 'All') {
                $query->where('storage', 'like', "%{$request->storage}%");
            }

            $sort = $request->sort ?? 'recommended';
            switch ($sort) {
                case 'price_low': $query->orderBy('price', 'asc'); break;
                case 'price_high': $query->orderBy('price', 'desc'); break;
                case 'latest': $query->orderBy('release_date', 'desc'); break;
                default: $query->orderBy('rating', 'desc'); break;
            }

            $results = $query->get();

            return Inertia::render('Home', [
                'recommended' => $results,
                'latest' => [],
                'budget' => [],
                'filters' => $request->only(['q', 'brand', 'ram', 'storage', 'sort']),
            ]);
        }

        return Inertia::render('Home', [
            'recommended' => $recommended,
            'latest' => $latest,
            'budget' => $budget,
            'filters' => $request->only(['q', 'brand', 'ram', 'storage', 'sort']),
        ]);
    }

    /**
     * Show all products
     */
    public function allProducts(Request $request)
    {
        $query = Phone::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('brand', 'like', "%{$q}%")
                    ->orWhere('model', 'like', "%{$q}%");
            });
        }

        if ($request->filled('brand') && $request->brand !== 'All') {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('ram') && $request->ram !== 'All') {
            $query->where('ram', $request->ram);
        }

        if ($request->filled('storage') && $request->storage !== 'All') {
            $query->where('storage', 'like', "%{$request->storage}%");
        }

        // Multi-sort: sorts is comma-separated, applied in order
        $sorts = $request->filled('sorts') ? explode(',', $request->sorts) : ['recommended'];
        foreach ($sorts as $sort) {
            switch (trim($sort)) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'latest':
                    $query->orderBy('release_date', 'desc');
                    break;
                case 'recommended':
                default:
                    $query->orderBy('rating', 'desc');
                    break;
            }
        }

        $phones = $query->get();

        return Inertia::render('Products', [
            'phones' => $phones,
            'filters' => $request->only(['q', 'brand', 'ram', 'storage', 'sorts']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $phone = Phone::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Phone add successfully',
            'data' => $phone,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $phone = Phone::with('reviews')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $phone,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $phone = Phone::findOrFail($id);
        $phone->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Phone updated successfully',
            'data' => $phone,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $phone = Phone::findOrFail($id);
        $phone->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Phone deleted successfully',
        ], 200);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:phones,id',
        ]);

        Phone::whereIn('id', $request->ids)->delete();

        return back()->with('success', 'Selected phones deleted successfully');
    }
}
