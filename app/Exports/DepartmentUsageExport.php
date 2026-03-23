<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class DepartmentUsageExport implements FromCollection, WithHeadings
{
    protected $timeframe;

    public function __construct($timeframe)
    {
        $this->timeframe = $timeframe;
    }

    public function collection()
    {
        $query = DB::table('request_items')
            ->join('consumable_requests', 'request_items.consumable_request_id', '=', 'consumable_requests.id')
            ->leftJoin('departments', 'consumable_requests.department_id', '=', 'departments.id')
            ->where('request_items.status', 'Fulfilled');

        if ($this->timeframe === 'this_month') {
            $query->whereMonth('request_items.updated_at', Carbon::now()->month)
                ->whereYear('request_items.updated_at', Carbon::now()->year);
        }

        return $query->select(
            DB::raw('COALESCE(departments.name, "Unassigned / Old") as Department'),
            DB::raw('SUM(request_items.fulfilled_quantity) as Total_Items_Consumed')
        )
            ->groupBy(DB::raw('COALESCE(departments.name, "Unassigned / Old")'))
            ->orderByDesc('Total_Items_Consumed')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Department Name',
            'Total Items Consumed',
        ];
    }
}
