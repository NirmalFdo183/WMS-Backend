<?php

namespace App\Http\Controllers;

use App\Models\Loading;
use App\Models\LoadListItem;
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

        // Low Stock Analysis
        $lowStockCount = \App\Models\Product::whereHas('batchStocks', function ($q) {
            $q->where('qty', '>', 0);
        })->get()->filter(function ($p) {
            // This is a bit expensive but matches the UI logic
            $stock = \App\Models\Batch_Stock::where('product_id', $p->id)->sum(DB::raw('qty + free_qty'));
            $pending = \App\Models\LoadListItem::whereHas('loading', fn ($q) => $q->where('status', 'pending'))
                ->whereIn('batch_id', \App\Models\Batch_Stock::where('product_id', $p->id)->pluck('id'))
                ->sum(DB::raw('qty + COALESCE(free_qty, 0)'));
            $total = $stock + $pending;

            return $total > 0 && $total <= 10;
        })->count();

        // Total Supply Cost (from Supplier Invoices)
        $totalSupplyCost = (float) \App\Models\SupplierInvoice::sum('total_bill_amount');

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
}
