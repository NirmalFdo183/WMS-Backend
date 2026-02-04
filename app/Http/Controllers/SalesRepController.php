<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SalesRepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salesReps = SalesRep::with(['supplier', 'route'])->get();
        return response()->json($salesReps);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rep_id' => 'required|string|unique:sales_reps,rep_id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'route_id' => 'required|exists:routes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $salesRep = SalesRep::create($request->all());

        return response()->json($salesRep, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $salesRep = SalesRep::with(['supplier', 'route'])->find($id);

        if (!$salesRep) {
            return response()->json(['message' => 'Sales Rep not found'], 404);
        }

        return response()->json($salesRep);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $salesRep = SalesRep::find($id);

        if (!$salesRep) {
            return response()->json(['message' => 'Sales Rep not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rep_id' => 'sometimes|required|string|unique:sales_reps,rep_id,' . $id,
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'name' => 'sometimes|required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'route_id' => 'sometimes|required|exists:routes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $salesRep->update($request->all());

        return response()->json($salesRep);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $salesRep = SalesRep::find($id);

        if (!$salesRep) {
            return response()->json(['message' => 'Sales Rep not found'], 404);
        }

        $salesRep->delete();

        return response()->json(['message' => 'Sales Rep deleted successfully']);
    }
}
