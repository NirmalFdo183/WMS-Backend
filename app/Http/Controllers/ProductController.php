<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('supplier')->withSum('batchStocks as stock', 'remain_qty')->get();

        // Include quantities from pending loading manifests as a separate field
        foreach ($products as $product) {
            $pendingQty = \App\Models\LoadListItem::whereHas('loading', function ($query) {
                $query->where('status', 'pending');
            })->whereIn('batch_id', \App\Models\Batch_Stock::where('product_id', $product->id)->pluck('id'))
                ->sum('qty');

            $product->shelf_stock = (int) ($product->stock ?? 0);
            $product->pending_stock = (int) $pendingQty;
            // Total stock for general display remains product->stock for backward compatibility if needed, 
            // but we'll use shelf_stock and pending_stock for breakdown
        }

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_code' => 'required|string|unique:products,material_code|max:255',
            'barcode' => 'required|string|unique:products,barcode|max:255',
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::withSum('batchStocks as stock', 'remain_qty')->findOrFail($id);

        $pendingQty = \App\Models\LoadListItem::whereHas('loading', function ($query) {
            $query->where('status', 'pending');
        })->whereIn('batch_id', \App\Models\Batch_Stock::where('product_id', $product->id)->pluck('id'))
            ->sum('qty');

        $product->shelf_stock = (int) ($product->stock ?? 0);
        $product->pending_stock = (int) $pendingQty;

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'material_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'barcode' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id),
            ],
            'name' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
