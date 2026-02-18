<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Loading;
use App\Models\LoadingReturn;
use App\Models\Batch_Stock;
use Illuminate\Support\Facades\DB;

class LoadingReturnController extends Controller
{
    /**
     * Store a newly created return in storage.
     */
    public function store(Request $request, $loadingId)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batch__stocks,id',
            'qty' => 'required|integer|min:1',
            'return_date' => 'required|date',
            'reason' => 'nullable|string|max:255',
        ]);

        $loading = Loading::findOrFail($loadingId);

        // Verify that this batch was actually part of the loading
        // and that we aren't returning more than was loaded
        $loadingItem = $loading->loadingItems()->where('batch_id', $validated['batch_id'])->first();

        if (!$loadingItem) {
            return response()->json(['message' => 'This batch was not found in the specified loading.'], 422);
        }

        // Calculate total already returned for this item
        $alreadyReturned = LoadingReturn::where('loading_id', $loadingId)
            ->where('batch_id', $validated['batch_id'])
            ->sum('qty');

        if (($alreadyReturned + $validated['qty']) > $loadingItem->qty) {
             return response()->json(['message' => 'Cannot return more items than were originally loaded (minus previous returns).'], 422);
        }

        return DB::transaction(function () use ($validated, $loadingId) {
            // 1. Create Return Record
            $return = LoadingReturn::create([
                'loading_id' => $loadingId,
                'batch_id' => $validated['batch_id'],
                'qty' => $validated['qty'],
                'return_date' => $validated['return_date'],
                'reason' => $validated['reason'] ?? null,
            ]);

            // 2. Update Batch Stock
            // Increment BOTH remain_qty (total available) AND returned_qty (priority pool)
            $batch = Batch_Stock::lockForUpdate()->find($validated['batch_id']);
            $batch->increment('remain_qty', $validated['qty']);
            $batch->increment('returned_qty', $validated['qty']);

            return response()->json($return, 201);
        });
    }

    /**
     * Display a listing of the returns.
     */
    public function index()
    {
        $returns = LoadingReturn::with(['loading', 'batchStock.product'])
            ->latest()
            ->get();
            
        return response()->json($returns);
    }
}
