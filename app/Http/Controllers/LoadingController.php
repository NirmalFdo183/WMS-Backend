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
            $loadings = Loading::with(['truck', 'route', 'driver', 'helper', 'cashCollector', 'loadingItems.batchStock.product'])->latest()->get();
            return response()->json($loadings);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching loadings: ' . $e->getMessage());
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $loading = Loading::create($request->except('items'));

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $batch = \App\Models\Batch_Stock::lockForUpdate()->find($itemData['batch_id']);
                    if ($batch->qty < $itemData['qty']) {
                        throw new \Exception("Insufficient stock for product " . ($batch->product->name ?? 'ID: '.$batch->id) . ". Available Qty: " . $batch->qty . ", Required Qty: " . $itemData['qty']);
                    }
                    if (($batch->free_qty ?? 0) < ($itemData['free_qty'] ?? 0)) {
                         throw new \Exception("Insufficient free stock for product " . ($batch->product->name ?? 'ID: '.$batch->id) . ". Available Free: " . ($batch->free_qty ?? 0) . ", Required Free: " . ($itemData['free_qty'] ?? 0));
                    }

                    $batch->decrement('qty', $itemData['qty']);
                    if (!empty($itemData['free_qty']) && $itemData['free_qty'] > 0) {
                        $batch->decrement('free_qty', $itemData['free_qty']);
                    }
                    
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
        $loading = Loading::with(['truck', 'route', 'driver', 'helper', 'cashCollector'])->find($id);

        if (!$loading) {
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

        if (!$loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'load_number' => 'sometimes|required|string|unique:loadings,load_number,' . $id,
            'truck_id' => 'sometimes|required|exists:trucks,id',
            'route_id' => 'sometimes|required|exists:routes,id',
            'prepared_date' => 'nullable|date',
            'loading_date' => 'nullable|date',
            'status' => 'in:pending,delivered,not_delivered',
            'driver_id' => 'nullable|exists:employees,id',
            'helper_id' => 'nullable|exists:employees,id',
            'cash_collector_id' => 'nullable|exists:employees,id',
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
                        $batch->increment('qty', $item->qty);
                        if ($item->free_qty > 0) {
                            $batch->increment('free_qty', $item->free_qty);
                        }
                    }
                }
            }
            // If moving FROM 'not_delivered' TO 'pending'/'delivered' -> Deduct Stock
            elseif ($oldStatus === 'not_delivered' && ($newStatus === 'pending' || $newStatus === 'delivered')) {
                foreach ($loading->loadingItems as $item) {
                    $batch = \App\Models\Batch_Stock::lockForUpdate()->find($item->batch_id);
                    if ($batch->qty < $item->qty) {
                         throw new \Exception("Insufficient stock to re-activate manifest for " . ($batch->product->name ?? 'item '.$item->id));
                    }
                    if (($batch->free_qty ?? 0) < ($item->free_qty ?? 0)) {
                         throw new \Exception("Insufficient free stock to re-activate manifest for " . ($batch->product->name ?? 'item '.$item->id));
                    }

                    $batch->decrement('qty', $item->qty);
                    if ($item->free_qty > 0) {
                        $batch->decrement('free_qty', $item->free_qty);
                    }
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

        if (!$loading) {
            return response()->json(['message' => 'Loading not found'], 404);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($loading) {
            // Restore stock if the loading was NOT already 'not_delivered' (cancelled)
            // If it was 'not_delivered', stock has already been restored by the status change logic.
            if ($loading->status !== 'not_delivered') {
                foreach ($loading->loadingItems as $item) {
                    $batch = \App\Models\Batch_Stock::find($item->batch_id);
                    if ($batch) {
                        $batch->increment('qty', $item->qty);
                         if ($item->free_qty > 0) {
                            $batch->increment('free_qty', $item->free_qty);
                        }
                    }
                }
            }

            $loading->delete();
            return response()->json(['message' => 'Loading deleted successfully']);
        });
    }
}
