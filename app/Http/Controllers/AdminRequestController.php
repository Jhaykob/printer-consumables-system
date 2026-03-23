<?php

namespace App\Http\Controllers;

use App\Models\ConsumableRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\RequestItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminRequestController extends Controller
{
    public function index()
    {
        // STRICT PERMISSION ENFORCEMENT: Check for the correct 'manage-requests' permission
        $user = Auth::user(); // <-- Changed from auth()->user()

        // Check if user exists first to prevent null errors, then check permissions
        if (!$user || (!$user->is_superuser && !$user->permissions->contains('name', 'manage-requests'))) {
            abort(403, 'UNAUTHORIZED: You do not have permission to fulfill requests.');
        }

        // Eager load EVERYTHING so the page loads blazingly fast
        $requests = ConsumableRequest::with([
            'user',
            'department',
            'location',
            'printer',
            'items.inventory.consumableType',
            'items.inventory.color'
        ])->latest()->paginate(15);

        return view('admin.requests.index', compact('requests'));
    }

    public function show($id)
    {
        // STRICT PERMISSION ENFORCEMENT
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || (!$user->is_superuser && !$user->permissions->contains('name', 'manage-requests'))) {
            abort(403, 'UNAUTHORIZED: You do not have permission to fulfill requests.');
        }

        // Fetch the request and eager load all the relationships we need
        $request = \App\Models\ConsumableRequest::with([
            'user',
            'department',
            'location',
            'printer',
            'items.inventory.consumableType',
            'items.inventory.color'
        ])->findOrFail($id);

        return view('admin.requests.show', compact('request'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || (!$user->is_superuser && !$user->permissions->contains('name', 'manage-requests'))) {
            abort(403, 'UNAUTHORIZED: You do not have permission to fulfill requests.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.status' => 'required|in:Pending,Fulfilled,Denied',
            'items.*.fulfilled_quantity' => 'required|integer|min:0',
            'items.*.reason' => 'nullable|string|max:255',
        ]);

        $consumableRequest = \App\Models\ConsumableRequest::with('items.inventory')->findOrFail($id);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $hasPending = false;
            $hasFulfilled = false;
            $hasDenied = false;

            foreach ($request->items as $itemId => $data) {
                $item = $consumableRequest->items->where('id', $itemId)->first();
                if (!$item || $item->status !== 'Pending') {
                    // Tally existing statuses for already processed items
                    if ($item && $item->status === 'Fulfilled') $hasFulfilled = true;
                    if ($item && $item->status === 'Denied') $hasDenied = true;
                    continue;
                }

                $newStatus = $data['status'];
                $qty = (int) $data['fulfilled_quantity'];
                $reason = $data['reason'] ?? null;

                if ($newStatus === 'Fulfilled') {
                    $inventory = $item->inventory;
                    if ($inventory->stock_level < $qty) {
                        throw new \Exception("Not enough stock for {$inventory->consumableType->name}.");
                    }
                    $inventory->stock_level -= $qty;
                    $inventory->save();

                    $item->status = 'Fulfilled';
                    $item->fulfilled_quantity = $qty;
                    $item->reason = $reason;
                    $hasFulfilled = true;
                } elseif ($newStatus === 'Denied') {
                    if (empty($reason)) {
                        throw new \Exception("A reason is required when denying an item.");
                    }
                    $item->status = 'Denied';
                    $item->fulfilled_quantity = 0;
                    $item->reason = $reason;
                    $hasDenied = true;
                } else {
                    // Status remains 'Pending' (Skipped)
                    $item->reason = $reason; // Save any notes they made before skipping
                    $hasPending = true;
                }

                $item->save();
            }

            // Determine parent request status dynamically
            if ($hasPending) {
                // If some are processed and some are pending, it's 'Processing'
                $consumableRequest->status = ($hasFulfilled || $hasDenied) ? 'Processing' : 'Pending';
            } elseif ($hasFulfilled && $hasDenied) {
                $consumableRequest->status = 'Partially Fulfilled';
            } elseif ($hasFulfilled) {
                $consumableRequest->status = 'Fulfilled';
            } else {
                $consumableRequest->status = 'Denied';
            }

            $consumableRequest->save();
            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Items processed successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function recall(\Illuminate\Http\Request $request, $id, $itemId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user || (!$user->is_superuser && !$user->permissions->contains('name', 'manage-requests'))) {
            abort(403);
        }

        $request->validate([
            'recall_action' => 'required|in:return,defective',
            'recall_reason' => 'required|string|max:255',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $item = \App\Models\RequestItem::with('inventory')->findOrFail($itemId);

            if ($item->status !== 'Fulfilled') {
                throw new \Exception("Only fulfilled items can be recalled.");
            }

            // Handle Inventory Adjustments
            if ($request->recall_action === 'return') {
                $item->inventory->stock_level += $item->fulfilled_quantity;
                $item->inventory->save();
                $actionNote = "[Returned to Stock]";
            } else {
                $actionNote = "[Marked Defective / Discarded]";
            }

            $item->status = 'Recalled';
            $item->reason = $actionNote . ' ' . $request->recall_reason;
            $item->fulfilled_quantity = 0;
            $item->save();

            // Re-evaluate parent status
            $consumableRequest = \App\Models\ConsumableRequest::with('items')->findOrFail($id);
            $statuses = $consumableRequest->items->pluck('status')->unique();

            if ($statuses->contains('Pending')) {
                $consumableRequest->status = 'Processing';
            } elseif ($statuses->contains('Fulfilled') && ($statuses->contains('Denied') || $statuses->contains('Recalled'))) {
                $consumableRequest->status = 'Partially Fulfilled';
            } elseif ($statuses->count() === 1 && $statuses->first() === 'Fulfilled') {
                $consumableRequest->status = 'Fulfilled';
            } else {
                // If everything is denied or recalled, mark it Denied/Closed
                $consumableRequest->status = 'Denied';
            }

            $consumableRequest->save();
            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Item successfully recalled.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
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
