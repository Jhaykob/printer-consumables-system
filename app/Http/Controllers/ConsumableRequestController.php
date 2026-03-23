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
        $departments = \App\Models\Department::all();
        $locations = \App\Models\PrinterLocation::all();

        // EAGER LOAD consumableTypes to link them to the frontend!
        $printers = \App\Models\Printer::with('consumableTypes')->get();
        $inventories = \App\Models\Inventory::with(['consumableType', 'color'])->get();

        $user = Auth::user();

        // CHECK FOR THE NEW PERMISSION
        $canSubmitOnBehalf = $user->is_superuser || $user->permissions->contains('name', 'submit-on-behalf');

        $users = $canSubmitOnBehalf ? \App\Models\User::orderBy('name')->get() : collect();

        return view('requests.create', compact('departments', 'locations', 'printers', 'inventories', 'users', 'canSubmitOnBehalf'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            // VALIDATION FIX: Check printer_locations table
            'location_id' => 'required|exists:printer_locations,id',
            'printer_id' => 'nullable|exists:printers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $submitter = Auth::user();
        $canSubmitOnBehalf = $submitter->is_superuser || $submitter->permissions->contains('name', 'submit-on-behalf');

        // Determine WHO the request actually belongs to
        $targetUserId = $submitter->id;
        $guestName = null;

        if ($canSubmitOnBehalf && $request->filled('is_on_behalf')) {
            if ($request->on_behalf_type === 'existing' && $request->filled('existing_user_id')) {
                // Admin selected an existing user
                $targetUserId = $request->existing_user_id;
            } elseif ($request->on_behalf_type === 'new' && $request->filled('new_user_name')) {
                // DO NOT CREATE A USER! Just log the admin's ID for tracking, and save the typed name.
                $targetUserId = $submitter->id;
                $guestName = $request->new_user_name;
            }
        }

        // Create the main request
        $consumableRequest = \App\Models\ConsumableRequest::create([
            'user_id' => $targetUserId,
            'guest_name' => $guestName, // Save the manual name here
            'department_id' => $request->department_id,
            'location_id' => $request->location_id,
            'printer_id' => $request->printer_id,
            'notes' => $request->notes,
            'status' => 'Pending'
        ]);

        // Attach the items
        foreach ($request->items as $item) {
            // Prevent duplicates on the backend just in case
            $exists = $consumableRequest->items()->where('inventory_id', $item['inventory_id'])->exists();
            if (!$exists) {
                \App\Models\RequestItem::create([
                    'consumable_request_id' => $consumableRequest->id,
                    'inventory_id' => $item['inventory_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'Pending'
                ]);
            }
        }

        return redirect()->route('requests.index')->with('success', 'Request submitted successfully.');
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
