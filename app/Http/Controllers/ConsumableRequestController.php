<?php

namespace App\Http\Controllers;

use App\Models\ConsumableRequest;
use App\Models\Inventory;
use App\Models\Printer;
use App\Models\PrinterLocation;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsumableRequestController extends Controller
{
    public function index()
    {
        // Users only see their own requests (unless we build the admin view later)
        $requests = ConsumableRequest::where('user_id', Auth::id())
            ->with('items.inventory.consumableType', 'items.inventory.color')
            ->latest()
            ->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        // Now, we only send Departments initially.
        $departments = Department::orderBy('name')->get();
        return view('requests.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'printer_location_id' => 'required|exists:printer_locations,id',
            'printer_id' => 'required|exists:printers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $consumableRequest = ConsumableRequest::create([
            'user_id' => Auth::id(),
            'department_id' => $request->department_id,
            'printer_location_id' => $request->printer_location_id,
            'printer_id' => $request->printer_id,
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        foreach ($request->items as $item) {
            $consumableRequest->items()->create([
                'inventory_id' => $item['inventory_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('requests.index')->with('status', 'Request submitted successfully!');
    }

    // --- NEW AJAX METHODS ---

    public function getLocationsByDepartment($departmentId)
    {
        // Find distinct locations that host printers for this specific department
        $locationIds = Printer::where('department_id', $departmentId)->pluck('printer_location_id')->unique();
        $locations = \App\Models\PrinterLocation::whereIn('id', $locationIds)->orderBy('name')->get();

        return response()->json($locations);
    }

    public function getPrintersByLocation($departmentId, $locationId)
    {
        // Get printers that match BOTH the department and the location
        $printers = Printer::where('department_id', $departmentId)
            ->where('printer_location_id', $locationId)
            ->orderBy('name')
            ->get();

        return response()->json($printers);
    }

    public function getInventory($printerId)
    {
        $printer = Printer::with('consumableTypes')->findOrFail($printerId);
        $typeIds = $printer->consumableTypes->pluck('id');

        $inventory = Inventory::with(['consumableType', 'color'])
            ->whereIn('consumable_type_id', $typeIds)
            ->get();

        return response()->json($inventory);
    }
}
