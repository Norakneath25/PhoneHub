<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Get all reviews for a specific phone
    public function index()
    {
        $reviews = Review::all();

        return response()->json([
            'status' => 'success',
            'data' => $reviews,
        ], 200);
    }

    public function getIndexById(string $id)
    {
        $review = Review::where('phone_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $review,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'phone_id' => 'required|exists:phones,id',
            'comment' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create([
            'phone_id' => $request->phone_id,
            'user_id' => $request->user_id, // no user log in yet use user_id for now
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review added successfully',
            'data' => $review,
        ], 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review deleted successfully',
        ], 200);
    }
}
