<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-inventory');

        $timeframe = $request->query('timeframe', 'all');

        // --- 1. DEPARTMENT CONSUMPTION (Bar Chart) ---
        $deptQuery = DB::table('request_items')
            ->join('consumable_requests', 'request_items.consumable_request_id', '=', 'consumable_requests.id')
            ->leftJoin('departments', 'consumable_requests.department_id', '=', 'departments.id')
            ->where('request_items.status', 'Fulfilled');

        if ($timeframe === 'this_month') {
            $deptQuery->whereMonth('request_items.updated_at', Carbon::now()->month)
                ->whereYear('request_items.updated_at', Carbon::now()->year);
        }

        $deptUsage = $deptQuery->select(
            DB::raw('COALESCE(departments.name, "Unassigned / Old") as name'),
            DB::raw('SUM(request_items.fulfilled_quantity) as total_qty')
        )
            ->groupBy(DB::raw('COALESCE(departments.name, "Unassigned / Old")'))
            ->orderByDesc('total_qty')
            ->get();

        $chartLabels = $deptUsage->pluck('name')->toJson();
        $chartData = $deptUsage->pluck('total_qty')->toJson();

        // --- 2. TOP CONSUMED ITEMS LEADERBOARD ---
        $topItemsQuery = DB::table('request_items')
            ->join('inventories', 'request_items.inventory_id', '=', 'inventories.id')
            ->join('consumable_types', 'inventories.consumable_type_id', '=', 'consumable_types.id')
            ->where('request_items.status', 'Fulfilled');

        if ($timeframe === 'this_month') {
            $topItemsQuery->whereMonth('request_items.updated_at', Carbon::now()->month)
                ->whereYear('request_items.updated_at', Carbon::now()->year);
        }

        $topItems = $topItemsQuery->select('consumable_types.name', DB::raw('SUM(request_items.fulfilled_quantity) as total_qty'))
            ->groupBy('consumable_types.name')
            ->orderByDesc('total_qty')
            ->take(5) // Only get the top 5
            ->get();

        // --- 3. STATUS BREAKDOWN (Doughnut Chart) ---
        $statusQuery = DB::table('request_items');

        if ($timeframe === 'this_month') {
            $statusQuery->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year);
        }

        $statusBreakdown = $statusQuery->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $statusLabels = $statusBreakdown->pluck('status')->toJson();
        $statusData = $statusBreakdown->pluck('count')->toJson();

        // --- 4. QUICK SUMMARY METRICS ---
        $lowStock = Inventory::with(['consumableType', 'color'])->whereColumn('stock_level', '<=', 'threshold')->get();
        $pendingCount = DB::table('request_items')->where('status', 'Pending')->count(); // Total pending right now

        return view('admin.reports.index', compact(
            'deptUsage',
            'lowStock',
            'chartLabels',
            'chartData',
            'timeframe',
            'topItems',
            'statusLabels',
            'statusData',
            'pendingCount'
        ));
    }
}
