<?php

namespace App\Http\Controllers;

use App\Models\Loading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoadingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $loadings = Loading::with(['truck', 'route', 'driver', 'helper', 'cashCollector', 'salesRep', 'loadingItems.batchStock.product'])->latest()->get();

            return response()->json($loadings);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching loadings: '.$e->getMessage());

            return response()->json(['message' => 'Server Error fetching loadings'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'load_number' => 'required|string|unique:loadings,load_number',
            'truck_id' => 'required|exists:trucks,id',
            'route_id' => 'required|exists:routes,id',
            'prepared_date' => 'nullable|date',
            'loading_date' => 'nullable|date',
            'status' => 'in:pending,delivered,not_delivered',
            'items' => 'nullable|array',
            'items.*.batch_id' => 'required|exists:batch__stocks,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.free_qty' => 'nullable|integer|min:0',
            'items.*.wh_price' => 'nullable|numeric',
            'items.*.net_price' => 'nullable|numeric',
            'driver_id' => 'nullable|exists:employees,id',
            'helper_id' => 'nullable|exists:employees,id',
            'cash_collector_id' => 'nullable|exists:employees,id',
            'sales_rep_id' => 'required|exists:sales_reps,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $loading = Loading::create($request->except('items'));

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $batch = \App\Models\Batch_Stock::lockForUpdate()->find($itemData['batch_id']);
                    $totalRequested = $itemData['qty'] ?? 0;

                    if ($totalRequested > $batch->remain_qty) { // Check against batch->remain_qty only
                        throw new \Exception('Insufficient units for product '.($batch->product->name ?? 'ID: '.$batch->id).'. Available: '.$batch->remain_qty.', Required: '.$totalRequested);
                    }

                    // Priority Logic: Deduct from 'returned_qty' first, then normal stock
                    // Note: 'remain_qty' holds the TOTAL (including returned), so we ALWAYS decrement remain_qty
                    // We also decrement 'returned_qty' if it exists, to keep that tracking accurate.

                    $returnedAvailable = $batch->returned_qty ?? 0;
                    
                    if ($returnedAvailable > 0) {
                        $deductFromReturns = min($returnedAvailable, $totalRequested);
                        $batch->decrement('returned_qty', $deductFromReturns);
                    }

                    // Always decrement the main pool
                    $batch->decrement('remain_qty', $totalRequested);

                    $loading->loadingItems()->create($itemData);
                }
            }

            return response()->json($loading->load('loadingItems.batchStock.product'), 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $loading = Loading::with(['truck', 'route', 'driver', 'helper', 'cashCollector', 'salesRep'])->find($id);

        if (! $loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        return response()->json($loading);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $loading = Loading::find($id);

        if (! $loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'load_number' => 'sometimes|required|string|unique:loadings,load_number,'.$id,
            'truck_id' => 'sometimes|required|exists:trucks,id',
            'route_id' => 'sometimes|required|exists:routes,id',
            'prepared_date' => 'nullable|date',
            'loading_date' => 'nullable|date',
            'status' => 'in:pending,delivered,not_delivered',
            'driver_id' => 'nullable|exists:employees,id',
            'helper_id' => 'nullable|exists:employees,id',
            'cash_collector_id' => 'nullable|exists:employees,id',
            'sales_rep_id' => 'sometimes|required|exists:sales_reps,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $loading) {
            $oldStatus = $loading->status;
            $newStatus = $request->status;

            $loading->update($request->all());

            // Handle stock restoration/deduction based on status change
            // 'not_delivered' is treated as cancelled/returned, so stock should be restored.
            // If moving FROM 'pending'/'delivered' TO 'not_delivered' -> Restore Stock
            if (($oldStatus === 'pending' || $oldStatus === 'delivered') && $newStatus === 'not_delivered') {
                foreach ($loading->loadingItems as $item) {
                    $batch = \App\Models\Batch_Stock::find($item->batch_id);
                    if ($batch) {
                        $totalToRestore = $item->qty;
                        $batch->increment('remain_qty', $totalToRestore); // Restore to main 'remain_qty' pool
                        
                        // NOTE: We do not restore 'returned_qty' here because we don't track if the specific 
                        // sold units came from the returned pool or fresh pool. 
                        // They essentially become "fresh" available stock again.
                    }
                }
            }
            // If moving FROM 'not_delivered' TO 'pending'/'delivered' -> Deduct Stock
            elseif ($oldStatus === 'not_delivered' && ($newStatus === 'pending' || $newStatus === 'delivered')) {
                foreach ($loading->loadingItems as $item) {
                    $batch = \App\Models\Batch_Stock::lockForUpdate()->find($item->batch_id);
                    $totalRequested = $item->qty ?? 0;
                    // $totalAvailable = ($batch->qty ?? 0) + ($batch->free_qty ?? 0); // free_qty is no longer part of available pool

                    if ($totalRequested > $batch->remain_qty) { // Check against batch->remain_qty only
                        throw new \Exception('Insufficient stock to re-activate manifest for '.($batch->product->name ?? 'item '.$item->id));
                    }

                    // Priority Logic: Deduct from 'returned_qty' first, then normal stock
                    
                    $returnedAvailable = $batch->returned_qty ?? 0;

                    if ($returnedAvailable > 0) {
                        $deductFromReturns = min($returnedAvailable, $totalRequested);
                        $batch->decrement('returned_qty', $deductFromReturns);
                    }

                    // Deduct the total requested from the main 'remain_qty' pool
                    $batch->decrement('remain_qty', $totalRequested);
                }
            }

            return response()->json($loading->load('loadingItems.batchStock.product'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $loading = Loading::find($id);

        if (! $loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($loading) {
            // Restore stock if the loading was NOT already 'not_delivered' (cancelled)
            // If it was 'not_delivered', stock has already been restored by the status change logic.
            if ($loading->status !== 'not_delivered') {
                foreach ($loading->loadingItems as $item) {
                    $batch = \App\Models\Batch_Stock::find($item->batch_id);
                    if ($batch) {
                        $totalToRestore = $item->qty;
                        $batch->increment('remain_qty', $totalToRestore);
                    }
                }
            }

            $loading->delete();

            return response()->json(['message' => 'Loading deleted successfully']);
        });
    }
}
