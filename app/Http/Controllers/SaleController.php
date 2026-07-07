<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Batch_Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    /**
     * Store a newly created sale and its items in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_time' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric',
            'status' => 'required|in:completed,draft',
            'discount' => 'nullable|numeric',
            'payment_type' => 'nullable|in:cash,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_id' => 'nullable|exists:batch__stocks,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric',
            'items.*.total' => 'required|numeric',
            'items.*.discount' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::create([
                'date_time' => $validated['date_time'],
                'user_id' => $validated['user_id'],
                'total' => $validated['total'],
                'status' => $validated['status'],
                'discount' => $validated['discount'] ?? 0,
                'payment_type' => $validated['payment_type'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'batch_id' => $item['batch_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'discount' => $item['discount'] ?? 0,
                ]);

                // Deduct stock if it's a completed sale and has a batch
                if ($validated['status'] === 'completed' && isset($item['batch_id'])) {
                    $batch = Batch_Stock::find($item['batch_id']);
                    if ($batch) {
                        $batch->remain_qty -= $item['qty'];
                        $batch->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale created successfully',
                'sale' => $sale->load('items')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sale: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified sale and its items from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($id);

            // Revert stock if it was completed
            if ($sale->status === 'completed') {
                foreach ($sale->items as $item) {
                    if ($item->batch_id) {
                        $batch = Batch_Stock::find($item->batch_id);
                        if ($batch) {
                            $batch->remain_qty += $item->qty;
                            $batch->save();
                        }
                    }
                }
            }

            // The sale_items will be deleted automatically due to the onDelete('cascade') in migration,
            // but we can also explicitly delete if preferred. The cascade handles it.
            $sale->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sale: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale: ' . $e->getMessage()
            ], 500);
        }
    }
}
