<?php

namespace App\Http\Controllers;

use App\Models\ConsumableRequest;
use App\Models\Inventory;
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
        // Pass available inventory so users know what they can request
        $inventory = Inventory::with(['consumableType.category', 'color'])->get();
        return view('requests.create', compact('inventory'));
    }

    public function store(Request $request)
    {
        // Validate the arrays submitted by the dynamic form
        $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Create the Header
        $consumableRequest = ConsumableRequest::create([
            'user_id' => Auth::id(),
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        // Create the Line Items
        foreach ($request->items as $item) {
            $consumableRequest->items()->create([
                'inventory_id' => $item['inventory_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        return redirect()->route('requests.index')->with('status', 'Request submitted successfully!');
    }
}
