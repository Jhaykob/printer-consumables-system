<?php

namespace App\Http\Controllers;

use App\Models\ConsumableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\RequestItem;
use Illuminate\Support\Facades\DB;

class AdminRequestController extends Controller
{
    public function index()
    {
        // Only users who can manage inventory can see this page
        Gate::authorize('manage-inventory');

        // Eager load EVERYTHING so the page loads blazingly fast
        $requests = ConsumableRequest::with([
            'user',
            'department',
            'location',
            'printer',
            'items.inventory.consumableType',
            'items.inventory.color'
        ])->latest()->get();

        return view('admin.requests.index', compact('requests'));
    }

    public function updateItem(Request $request, RequestItem $requestItem)
    {
        Gate::authorize('manage-inventory');

        $request->validate([
            'status' => 'required|in:Pending,Approved,Denied,Fulfilled,Recalled',
            'fulfilled_quantity' => 'required_if:status,Fulfilled|nullable|integer|min:1',
            'rejection_reason' => 'required_if:status,Denied|nullable|string',
            'recall_reason' => 'required_if:status,Recalled|nullable|string',
            'recall_action' => 'required_if:status,Recalled|in:restock,dispose|nullable',
        ]);

        $oldStatus = $requestItem->status;
        $newStatus = $request->status;
        $fulfilledQty = $request->fulfilled_quantity;

        // Prevent doing anything if nothing changed
        if ($oldStatus === $newStatus && $newStatus !== 'Fulfilled') return back();

        DB::beginTransaction();
        try {
            $inventory = $requestItem->inventory;

            // Amount we need to return if we are undoing a fulfillment
            $qtyToReturn = $requestItem->fulfilled_quantity ?? 0;

            // SCENARIO 1: Brand New Fulfillment (Deduct Stock)
            if ($newStatus === 'Fulfilled' && $oldStatus !== 'Fulfilled') {
                if ($inventory->stock_level < $fulfilledQty) {
                    throw new \Exception("Cannot fulfill {$fulfilledQty}. Only {$inventory->stock_level} in stock.");
                }
                $inventory->decrement('stock_level', $fulfilledQty);
            }

            // SCENARIO 2: Adjusting an ALREADY Fulfilled Item (e.g. changing qty from 2 to 3)
            if ($newStatus === 'Fulfilled' && $oldStatus === 'Fulfilled' && $qtyToReturn != $fulfilledQty) {
                $difference = $fulfilledQty - $qtyToReturn;
                if ($inventory->stock_level < $difference) {
                    throw new \Exception("Not enough extra stock. Need {$difference} more, but only {$inventory->stock_level} available.");
                }
                $inventory->decrement('stock_level', $difference); // Decrementing a negative number adds to stock!
            }

            // SCENARIO 3: Recalling an item
            if ($newStatus === 'Recalled' && $oldStatus === 'Fulfilled') {
                if ($request->recall_action === 'restock') {
                    $inventory->increment('stock_level', $qtyToReturn);
                }
            }

            // SCENARIO 4: Completely Undoing a Fulfillment (Changing from Fulfilled to Pending/Approved)
            if ($oldStatus === 'Fulfilled' && !in_array($newStatus, ['Fulfilled', 'Recalled'])) {
                $inventory->increment('stock_level', $qtyToReturn);
            }

            // Save the new data
            $requestItem->update([
                'status' => $newStatus,
                'fulfilled_quantity' => $newStatus === 'Fulfilled' ? $fulfilledQty : $requestItem->fulfilled_quantity,
                'rejection_reason' => $newStatus === 'Denied' ? $request->rejection_reason : null,
                'recall_reason' => $newStatus === 'Recalled' ? $request->recall_reason : null,
                'recall_action' => $newStatus === 'Recalled' ? $request->recall_action : null,
            ]);

            DB::commit();
            return back()->with('status', 'Item updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['stock_error' => $e->getMessage()]);
        }
    }
}
