<?php

namespace App\Http\Controllers;

use App\Models\Loading;
use App\Models\LoadListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats()
    {
        $deliveredLoadings = Loading::where('status', 'delivered')->get();
        $deliveredLoadingIds = $deliveredLoadings->pluck('id');

        $stats = LoadListItem::whereIn('loading_id', $deliveredLoadingIds)
            ->join('batch__stocks', 'load_list_items.batch_id', '=', 'batch__stocks.id')
            ->select(
                DB::raw('SUM(load_list_items.qty * load_list_items.wh_price) as total_revenue'),
                DB::raw('SUM(load_list_items.qty * batch__stocks.netprice) as total_cost'),
                DB::raw('SUM(load_list_items.qty * (load_list_items.wh_price - batch__stocks.netprice)) as total_profit')
            )
            ->first();

        // Get daily profit for the last 30 days
        $dailyStats = Loading::where('status', 'delivered')
            ->where('loading_date', '>=', now()->subDays(30))
            ->join('load_list_items', 'loadings.id', '=', 'load_list_items.loading_id')
            ->join('batch__stocks', 'load_list_items.batch_id', '=', 'batch__stocks.id')
            ->select(
                'loadings.loading_date',
                DB::raw('SUM(load_list_items.qty * load_list_items.wh_price) as revenue'),
                DB::raw('SUM(load_list_items.qty * (load_list_items.wh_price - batch__stocks.netprice)) as profit')
            )
            ->groupBy('loadings.loading_date')
            ->orderBy('loadings.loading_date')
            ->get();

        // Low Stock Analysis
        $lowStockCount = \App\Models\Product::whereHas('batchStocks', function($q) {
            $q->where('qty', '>', 0);
        })->get()->filter(function($p) {
            // This is a bit expensive but matches the UI logic
            $stock = \App\Models\Batch_Stock::where('product_id', $p->id)->sum('qty');
            $pending = \App\Models\LoadListItem::whereHas('loading', fn($q) => $q->where('status', 'pending'))
                ->whereIn('batch_id', \App\Models\Batch_Stock::where('product_id', $p->id)->pluck('id'))
                ->sum(DB::raw('qty + COALESCE(free_qty, 0)'));
            $total = $stock + $pending;
            return $total > 0 && $total <= 10;
        })->count();

        // Total Supply Cost (from Supplier Invoices)
        $totalSupplyCost = (float) \App\Models\SupplierInvoice::sum('total_bill_amount');

        return response()->json([
            'total_revenue' => (float) ($stats->total_revenue ?? 0),
            'total_cost' => (float) ($stats->total_cost ?? 0),
            'total_profit' => (float) ($stats->total_profit ?? 0),
            'total_supply_cost' => $totalSupplyCost,
            'manifest_count' => $deliveredLoadings->count(),
            'daily_stats' => $dailyStats,
            'low_stock_count' => $lowStockCount
        ]);
    }
}
