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
            'pack_size' => 'required|integer|min:0',
            'extra_units' => 'sometimes|integer',
            'qty' => 'required|integer',
            'retail_price' => 'required|numeric',
            'netprice' => 'required|numeric',
            'expiry_date' => 'nullable|date',
        ]);

        $batchStock = Batch_Stock::create($validated);

        $this->updateInvoiceTotal($batchStock->supplier_invoice_id);

        return response()->json($batchStock->load('product'), 201);
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
            'pack_size' => 'sometimes|integer|min:0',
            'extra_units' => 'sometimes|integer',
            'qty' => 'sometimes|integer',
            'retail_price' => 'sometimes|numeric',
            'netprice' => 'sometimes|numeric',
            'expiry_date' => 'nullable|date',
        ]);

        $batchStock->update($validated);

        $this->updateInvoiceTotal($batchStock->supplier_invoice_id);

        return response()->json($batchStock->load('product'));
    }

    public function destroy(string $id)
    {
        $batchStock = Batch_Stock::findOrFail($id);
        $invoiceId = $batchStock->supplier_invoice_id;
        $batchStock->delete();

        $this->updateInvoiceTotal($invoiceId);

        return response()->json(null, 204);
    }

    protected function updateInvoiceTotal($invoiceId)
    {
        $invoice = \App\Models\SupplierInvoice::find($invoiceId);
        if ($invoice) {
            // Re-calculate sum of all items
            $total = $invoice->batchStocks()->sum(\DB::raw('qty * netprice'));
            // Subtract discount
            $finalTotal = $total - $invoice->discount;
            $invoice->update(['total_bill_amount' => $finalTotal]);
        }
    }

    public function byProduct($productId)
    {
        $batches = Batch_Stock::where('product_id', $productId)
            ->where('qty', '>', 0) // Only show batches with stock
            ->with(['supplierInvoice'])
            ->get();

        return response()->json($batches);
    }
}
