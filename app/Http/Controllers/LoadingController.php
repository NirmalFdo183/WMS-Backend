<?php

namespace App\Http\Controllers;

use App\Models\Loading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loadings = Loading::with(['truck', 'route', 'loadingItems'])->get();
        return response()->json($loadings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'load_number' => 'required|string|unique:loadings,load_number',
            'truck_id' => 'required|exists:trucks,id',
            'route_id' => 'required|exists:routes,id',
            'prepared_date' => 'nullable|date',
            'loading_date' => 'nullable|date',
            'status' => 'in:pending,delivered,not_delivered',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loading = Loading::create($request->all());

        return response()->json($loading, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loading = Loading::with(['truck', 'route'])->find($id);

        if (!$loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        return response()->json($loading);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loading = Loading::find($id);

        if (!$loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'load_number' => 'sometimes|required|string|unique:loadings,load_number,' . $id,
            'truck_id' => 'sometimes|required|exists:trucks,id',
            'route_id' => 'sometimes|required|exists:routes,id',
            'prepared_date' => 'nullable|date',
            'loading_date' => 'nullable|date',
            'status' => 'in:pending,delivered,not_delivered',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $loading->update($request->all());

        return response()->json($loading);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loading = Loading::find($id);

        if (!$loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        $loading->delete();

        return response()->json(['message' => 'Loading deleted successfully']);
    }
}
