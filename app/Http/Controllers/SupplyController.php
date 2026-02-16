<?php

namespace App\Http\Controllers;

use App\Models\Batch_Stock;
use App\Models\SupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyController extends Controller
{
    /**
     * Store a newly created supply (invoice + batch items) in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'total_bill_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.no_cases' => 'required|integer|min:0',
            'items.*.pack_size' => 'required|integer|min:0',
            'items.*.extra_units' => 'sometimes|integer|min:0',
            'items.*.qty' => 'required|integer|min:0',
            'items.*.free_qty' => 'sometimes|integer|min:0',
            'items.*.retail_price' => 'required|numeric|min:0',
            'items.*.netprice' => 'required|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // Calculate total bill amount or use the provided one
                $calculatedTotal = collect($validated['items'])->reduce(function ($carry, $item) {
                    return $carry + ($item['qty'] * $item['netprice']);
                }, 0);
                
                $totalBillAmount = $validated['total_bill_amount'] ?? $calculatedTotal;

                // 1. Create the Supplier Invoice
                $invoice = SupplierInvoice::create([
                    'supplier_id' => $validated['supplier_id'],
                    'invoice_number' => $validated['invoice_number'],
                    'invoice_date' => $validated['invoice_date'],
                    'total_bill_amount' => $totalBillAmount,
                ]);

                // 2. Create the Batch Stocks
                foreach ($validated['items'] as $item) {
                    Batch_Stock::create([
                        'product_id' => $item['product_id'],
                        'supplier_invoice_id' => $invoice->id,
                        'no_cases' => $item['no_cases'],
                        'pack_size' => $item['pack_size'],
                        'extra_units' => $item['extra_units'] ?? 0,
                        'qty' => $item['qty'],
                        'free_qty' => $item['free_qty'] ?? 0,
                        'retail_price' => $item['retail_price'],
                        'netprice' => $item['netprice'],
                        'expiry_date' => $item['expiry_date'],
                    ]);
                }

                return response()->json([
                    'message' => 'Supply recorded successfully',
                    'invoice' => $invoice->load(['supplier', 'batchStocks.product'])
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to record supply',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
