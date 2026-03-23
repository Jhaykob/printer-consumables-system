<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // STRICT PERMISSION ENFORCEMENT
        $user = Auth::user(); // Changed from auth()->user()
        if (!$user->is_superuser && !$user->permissions->contains('name', 'view-dashboard')) {
            abort(403, 'UNAUTHORIZED: You do not have permission to view System Analytics.');
        }

        $timeframe = $request->query('timeframe', 'month');
        $startDate = null;
        $endDate = null;

        if ($timeframe === 'custom') {
            $startDate = $request->query('start_date') ? \Carbon\Carbon::parse($request->query('start_date'))->startOfDay() : now()->startOfMonth();
            $endDate = $request->query('end_date') ? \Carbon\Carbon::parse($request->query('end_date'))->endOfDay() : now()->endOfDay();
        } elseif ($timeframe !== 'all') {
            $startDate = match ($timeframe) {
                'today' => now()->startOfDay(),
                'week'  => now()->subDays(7)->startOfDay(),
                'month' => now()->startOfMonth(),
                'year'  => now()->startOfYear(),
            };
            $endDate = now()->endOfDay();
        }

        // 1. Department Consumption
        $deptUsage = DB::table('request_items')
            ->join('consumable_requests', 'request_items.consumable_request_id', '=', 'consumable_requests.id')
            ->leftJoin('departments', 'consumable_requests.department_id', '=', 'departments.id')
            ->where('request_items.status', 'Fulfilled')
            ->when($timeframe !== 'all', fn($q) => $q->whereBetween('request_items.updated_at', [$startDate, $endDate]))
            ->select(DB::raw('COALESCE(departments.name, "Unassigned / Old") as name'), DB::raw('SUM(COALESCE(request_items.fulfilled_quantity, request_items.quantity)) as total_qty'))
            ->groupBy(DB::raw('COALESCE(departments.name, "Unassigned / Old")'))
            ->orderByDesc('total_qty')
            ->get();

        $chartLabels = $deptUsage->pluck('name')->toJson();
        $chartData = $deptUsage->pluck('total_qty')->toJson();

        // 2. Top Consumed Items
        $topItems = DB::table('request_items')
            ->join('inventories', 'request_items.inventory_id', '=', 'inventories.id')
            ->join('consumable_types', 'inventories.consumable_type_id', '=', 'consumable_types.id')
            ->where('request_items.status', 'Fulfilled')
            ->when($timeframe !== 'all', fn($q) => $q->whereBetween('request_items.updated_at', [$startDate, $endDate]))
            ->select('consumable_types.name', DB::raw('SUM(COALESCE(request_items.fulfilled_quantity, request_items.quantity)) as total_qty'))
            ->groupBy('consumable_types.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 3. Status Breakdown
        $statusBreakdown = DB::table('request_items')
            ->when($timeframe !== 'all', fn($q) => $q->whereBetween('updated_at', [$startDate, $endDate]))
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $statusLabels = $statusBreakdown->pluck('status')->toJson();
        $statusData = $statusBreakdown->pluck('count')->toJson();

        $lowStock = \App\Models\Inventory::with(['consumableType', 'color'])->whereColumn('stock_level', '<=', 'threshold')->get();
        $pendingCount = DB::table('request_items')->where('status', 'Pending')->count();

        return view('admin.reports.index', compact('deptUsage', 'lowStock', 'chartLabels', 'chartData', 'timeframe', 'topItems', 'statusLabels', 'statusData', 'pendingCount'));
    }

    public function printReport(Request $request)
    {
        // STRICT PERMISSION ENFORCEMENT
        $user = Auth::user(); // Changed from auth()->user()
        if (!$user->is_superuser && !$user->permissions->contains('name', 'generate-reports')) {
            abort(403, 'UNAUTHORIZED: You do not have permission to generate reports.');
        }

        $timeframe = $request->query('timeframe', 'all');
        $startDate = null;
        $endDate = null;

        if ($timeframe === 'custom') {
            $startDate = $request->query('start_date') ? \Carbon\Carbon::parse($request->query('start_date'))->startOfDay() : now()->startOfMonth();
            $endDate = $request->query('end_date') ? \Carbon\Carbon::parse($request->query('end_date'))->endOfDay() : now()->endOfDay();
        } elseif ($timeframe !== 'all') {
            $startDate = match ($timeframe) {
                'today' => now()->startOfDay(),
                'week'  => now()->subDays(7)->startOfDay(),
                'month' => now()->startOfMonth(),
                'year'  => now()->startOfYear(),
            };
            $endDate = now()->endOfDay();
        }

        // 1. Detailed Activity Log
        $allActivity = DB::table('request_items')
            ->join('consumable_requests', 'request_items.consumable_request_id', '=', 'consumable_requests.id')
            ->leftJoin('departments', 'consumable_requests.department_id', '=', 'departments.id')
            ->join('inventories', 'request_items.inventory_id', '=', 'inventories.id')
            ->join('consumable_types', 'inventories.consumable_type_id', '=', 'consumable_types.id')
            ->when($timeframe !== 'all', fn($q) => $q->whereBetween('request_items.updated_at', [$startDate, $endDate]))
            ->select(
                'request_items.status',
                'request_items.quantity',
                'request_items.fulfilled_quantity',
                'request_items.updated_at',
                'consumable_types.name as item_name',
                DB::raw('COALESCE(departments.name, "N/A") as dept_name')
            )
            ->orderByDesc('request_items.updated_at')
            ->get();

        // 2. Global Status Totals
        $statusTotals = DB::table('request_items')
            ->when($timeframe !== 'all', fn($q) => $q->whereBetween('updated_at', [$startDate, $endDate]))
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // 3. High-to-Low Consumption Leaderboard
        $consumptionStats = DB::table('request_items')
            ->join('inventories', 'request_items.inventory_id', '=', 'inventories.id')
            ->join('consumable_types', 'inventories.consumable_type_id', '=', 'consumable_types.id')
            ->where('request_items.status', 'Fulfilled')
            ->when($timeframe !== 'all', fn($q) => $q->whereBetween('request_items.updated_at', [$startDate, $endDate]))
            ->select(
                'consumable_types.name',
                DB::raw('SUM(COALESCE(request_items.fulfilled_quantity, request_items.quantity)) as total_consumed'),
                DB::raw('ROUND(SUM(COALESCE(request_items.fulfilled_quantity, request_items.quantity)) / GREATEST(COUNT(DISTINCT DATE_FORMAT(request_items.updated_at, "%Y-%m")), 1), 2) as avg_per_month')
            )
            ->groupBy('consumable_types.name')
            ->orderByDesc('total_consumed')
            ->get();

        $lowStock = \App\Models\Inventory::with(['consumableType', 'color'])->whereColumn('stock_level', '<=', 'threshold')->get();

        $dateLabel = '';
        if ($timeframe === 'custom') {
            $dateLabel = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        }

        return view('admin.reports.print', compact('allActivity', 'statusTotals', 'consumptionStats', 'lowStock', 'timeframe', 'dateLabel'));
    }
}
