<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TruckController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Truck::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'licence_plate_no' => 'required|string|unique:trucks,licence_plate_no|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $truck = Truck::create($validated);

        return response()->json($truck, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $truck = Truck::findOrFail($id);
        return response()->json($truck);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $truck = Truck::findOrFail($id);

        $validated = $request->validate([
            'licence_plate_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trucks')->ignore($truck->id),
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $truck->update($validated);

        return response()->json($truck);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $truck = Truck::findOrFail($id);
        $truck->delete();

        return response()->json(null, 204);
    }
}
