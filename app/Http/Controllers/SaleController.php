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
     * Display a listing of sales.
     */
    public function index()
    {
        try {
            $sales = Sale::with(['user', 'items.product', 'items.batchStock.product'])->latest()->get();
            return response()->json($sales);
        } catch (\Exception $e) {
            Log::error('Error fetching sales: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error fetching sales'], 500);
        }
    }

    /**
     * Display the specified sale.
     */
    public function show($id)
    {
        try {
            $sale = Sale::with(['user', 'items.product', 'items.batchStock.product'])->findOrFail($id);
            return response()->json($sale);
        } catch (\Exception $e) {
            Log::error('Error fetching sale: ' . $e->getMessage());
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }

    /**
     * Store a newly created sale and its items in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_time' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'payment_type' => 'nullable|in:cash,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_id' => 'nullable|exists:batch__stocks,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.retail_price' => 'required|numeric',
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
                'discount' => $validated['discount'] ?? 0,
                'payment_type' => $validated['payment_type'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $batch = Batch_Stock::lockForUpdate()->find($item['batch_id']);
                $totalRequested = $item['qty'] ?? 0;

                if ($batch) {
                    if ($totalRequested > $batch->remain_qty) {
                         throw new \Exception('Insufficient units for product '.($batch->product->name ?? 'ID: '.$batch->id).'. Available: '.$batch->remain_qty.', Required: '.$totalRequested);
                    }

                    // Priority Logic: Deduct from 'returned_qty' first, then normal stock
                    $returnedAvailable = $batch->returned_qty ?? 0;
                    if ($returnedAvailable > 0) {
                        $deductFromReturns = min($returnedAvailable, $totalRequested);
                        $batch->decrement('returned_qty', $deductFromReturns);
                    }

                    // Always decrement the main pool
                    $batch->decrement('remain_qty', $totalRequested);
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'batch_id' => $item['batch_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'retail_price' => $item['retail_price'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'discount' => $item['discount'] ?? 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale created successfully',
                'sale' => $sale->load(['items.product', 'items.batchStock.product'])
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

            // Always revert stock on deletion
            foreach ($sale->items as $item) {
                if ($item->batch_id) {
                    $batch = Batch_Stock::find($item->batch_id);
                    if ($batch) {
                        $batch->increment('remain_qty', $item->qty);
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
