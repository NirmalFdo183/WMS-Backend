<?php

namespace App\Http\Controllers;

use App\Models\Batch_Stock;
use App\Models\Loading;
use App\Models\LoadListItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        $deliveredLoadings = Loading::where('status', 'delivered')->get();
        $deliveredLoadingIds = $deliveredLoadings->pluck('id');

        $stats = LoadListItem::whereIn('loading_id', $deliveredLoadingIds)
            ->select(
                DB::raw('SUM(load_list_items.qty * load_list_items.net_price) as total_revenue')
            )
            ->first();

        // Total Supply Cost (from Supplier Invoices)
        $totalSupplyCost = (float) \App\Models\SupplierInvoice::sum('total_bill_amount');

        // Profit is a flat 5% commission on the total supply amount
        $totalProfit = $totalSupplyCost * 0.05;

        // Get daily revenue from delivered loadings
        $dailyRevenue = Loading::where('status', 'delivered')
            ->where('loading_date', '>=', now()->subDays(30))
            ->join('load_list_items', 'loadings.id', '=', 'load_list_items.loading_id')
            ->select(
                'loadings.loading_date as date',
                DB::raw('SUM(load_list_items.qty * load_list_items.net_price) as revenue')
            )
            ->groupBy('loadings.loading_date')
            ->get()
            ->keyBy('date');

        // Get daily profit (commission) from supply entries
        $dailyProfit = \App\Models\SupplierInvoice::where('invoice_date', '>=', now()->subDays(30))
            ->select(
                'invoice_date as date',
                DB::raw('SUM(total_bill_amount * 0.05) as profit')
            )
            ->groupBy('invoice_date')
            ->get()
            ->keyBy('date');

        // Combine for chart
        $dates = $dailyRevenue->keys()->concat($dailyProfit->keys())->unique()->sort();
        $dailyStats = $dates->map(function ($date) use ($dailyRevenue, $dailyProfit) {

            return [
                'loading_date' => $date, // Kept key name for frontend compatibility
                'revenue' => (float) ($dailyRevenue[$date]->revenue ?? 0),
                'profit' => (float) ($dailyProfit[$date]->profit ?? 0),
            ];
        })->values();

        // Low Stock Analysis - Matches the threshold in Product.tsx (50 units)
        // Optimized: Calculate aggregated stock in 2 lightweight queries instead of 2N+1 queries
        $shelfStocks = \App\Models\Batch_Stock::groupBy('product_id')
            ->selectRaw('product_id, SUM(remain_qty) as total_stock')
            ->pluck('total_stock', 'product_id');

        $pendingStocks = \App\Models\LoadListItem::query()
            ->join('loadings', 'load_list_items.loading_id', '=', 'loadings.id')
            ->join('batch__stocks', 'load_list_items.batch_id', '=', 'batch__stocks.id')
            ->where('loadings.status', 'pending')
            ->groupBy('batch__stocks.product_id')
            ->selectRaw('batch__stocks.product_id, SUM(load_list_items.qty) as total_pending')
            ->pluck('total_pending', 'product_id');

        $allProductIds = $shelfStocks->keys()->concat($pendingStocks->keys())->unique();
        $lowStockCount = 0;
        foreach ($allProductIds as $pid) {
            $total = (int) ($shelfStocks[$pid] ?? 0) + (int) ($pendingStocks[$pid] ?? 0);
            if ($total > 0 && $total <= 50) {
                $lowStockCount++;
            }
        }

        return response()->json([
            'total_revenue' => (float) ($stats->total_revenue ?? 0),
            'total_cost' => (float) ($stats->total_revenue ?? 0), // Cost = Revenue because selling at net price
            'total_profit' => $totalProfit,
            'total_supply_cost' => $totalSupplyCost,
            'manifest_count' => $deliveredLoadings->count(),
            'daily_stats' => $dailyStats,
            'low_stock_count' => $lowStockCount,
        ]);
    }

    /**
     * Data for the main dashboard page, trimmed to what the page shows.
     *
     * Before, the page downloaded every product, every stock batch and 500
     * dispatches with seven related tables each, then filtered in the browser.
     * This returns the same records the page ends up using, so every number
     * on the dashboard stays the same, but far less data is sent.
     */
    public function overview(Request $request)
    {
        $lowStockLimit = 50;   // same as LOW_STOCK_LIMIT in Dashboard.tsx
        $expiryWindowDays = 90; // same as EXPIRY_WINDOW_DAYS in Dashboard.tsx
        $loadingsLimit = min(max((int) $request->input('limit', 500), 1), 500);

        // Low stock: only products at or below the limit (same rule as before).
        $products = Product::query()
            ->select(['id', 'name', 'material_code'])
            ->withSum('batchStocks as stock', 'remain_qty')
            ->get()
            ->map(function ($product) {
                $shelfStock = (int) ($product->stock ?? 0);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'material_code' => $product->material_code,
                    'shelf_stock' => $shelfStock,
                    'total_units' => $shelfStock,
                ];
            })
            ->filter(fn ($product) => $product['total_units'] <= $lowStockLimit)
            ->values();

        // Expiring soon: batches still in stock that expire inside the window.
        // Two extra days of margin cover time-zone differences; the page applies
        // the exact cut-off itself, as it always has.
        $batches = Batch_Stock::query()
            ->select(['id', 'product_id', 'remain_qty', 'netprice', 'expiry_date'])
            ->with('product:id,name,material_code')
            ->whereNotNull('expiry_date')
            ->where('remain_qty', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays($expiryWindowDays + 2)->toDateString())
            ->get();

        // Dispatches: the same latest records as before, but only with the
        // truck, route and item quantities the page displays.
        $loadings = Loading::query()
            ->select(['id', 'load_number', 'truck_id', 'route_id', 'prepared_date', 'loading_date', 'status', 'created_at'])
            ->with([
                'truck',
                'route',
                'loadingItems:id,loading_id,qty',
            ])
            ->latest()
            ->limit($loadingsLimit)
            ->get();

        return response()->json([
            'products' => $products,
            'batches' => $batches,
            'loadings' => $loadings,
        ]);
    }
}
