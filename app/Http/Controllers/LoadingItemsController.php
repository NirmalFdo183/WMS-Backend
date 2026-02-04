<?php

namespace App\Http\Controllers;

use App\Models\LoadListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoadingItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = LoadListItem::with(['loading', 'batchStock'])->get();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loading_id' => 'required|exists:loadings,id',
            'batch_id' => 'required|exists:batch__stocks,id',
            'qty' => 'required|integer',
            'free_qty' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = LoadListItem::create($request->all());

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = LoadListItem::with(['loading', 'batchStock'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Loading item not found'], 404);
        }

        return response()->json($item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = LoadListItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Loading item not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'loading_id' => 'sometimes|required|exists:loadings,id',
            'batch_id' => 'sometimes|required|exists:batch__stocks,id',
            'qty' => 'sometimes|required|integer',
            'free_qty' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item->update($request->all());

        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = LoadListItem::find($id);

        if (!$item) {
            return response()->json(['message' => 'Loading item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Loading item deleted successfully']);
    }
}
