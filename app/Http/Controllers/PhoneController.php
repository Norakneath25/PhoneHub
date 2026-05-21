<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use Illuminate\Http\Request;

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
