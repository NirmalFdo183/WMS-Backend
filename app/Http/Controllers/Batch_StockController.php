<?php

namespace App\Http\Controllers;

use App\Models\Batch_Stock;
use Illuminate\Http\Request;

class Batch_StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Batch_Stock::with(['product', 'supplierInvoice'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_invoice_id' => 'required|exists:supplier_invoices,id',
            'no_cases' => 'required|integer',
            'pack_size' => 'required|integer',
            'qty' => 'required|integer',
            'retail_price' => 'required|numeric',
            'netprice' => 'required|numeric',
            'expiry_date' => 'nullable|date',
        ]);

        $batchStock = Batch_Stock::create($validated);

        return response()->json($batchStock, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batchStock = Batch_Stock::with(['product', 'supplierInvoice'])->findOrFail($id);

        return response()->json($batchStock);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $batchStock = Batch_Stock::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'supplier_invoice_id' => 'sometimes|exists:supplier_invoices,id',
            'no_cases' => 'sometimes|integer',
            'pack_size' => 'sometimes|integer',
            'qty' => 'sometimes|integer',
            'retail_price' => 'sometimes|numeric',
            'netprice' => 'sometimes|numeric',
            'expiry_date' => 'nullable|date',
        ]);

        $batchStock->update($validated);

        return response()->json($batchStock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $batchStock = Batch_Stock::findOrFail($id);
        $batchStock->delete();

        return response()->json(null, 204);
    }
}
