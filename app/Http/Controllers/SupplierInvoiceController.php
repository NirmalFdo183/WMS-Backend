<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvoice;
use Illuminate\Http\Request;

class SupplierInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = SupplierInvoice::with('supplier')->latest()->get();
        return response()->json($invoices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'total_bill_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $invoice = SupplierInvoice::create($validated);

        return response()->json($invoice->load('supplier'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = SupplierInvoice::with(['supplier', 'batchStocks.product'])->findOrFail($id);
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = SupplierInvoice::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'invoice_number' => 'sometimes|required|string|unique:supplier_invoices,invoice_number,' . $id,
            'invoice_date' => 'sometimes|required|date',
            'total_bill_amount' => 'sometimes|required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $invoice->update($validated);

        return response()->json($invoice->load('supplier'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = SupplierInvoice::findOrFail($id);
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($invoice) {
            // Delete associated batch stocks first to avoid foreign key constraints
            $invoice->batchStocks()->delete();
            $invoice->delete();
        });

        return response()->json(null, 204);
    }

    public function totalSum()
    {
        $total = (float) SupplierInvoice::sum('total_bill_amount');
        return response()->json(['total' => $total]);
    }
}
