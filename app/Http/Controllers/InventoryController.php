<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ConsumableType;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    public function index()
    {
        // Eager load relationships for the table
        $inventories = Inventory::with(['consumableType.category', 'color'])->get();

        // Load data for the form dropdowns
        $types = ConsumableType::with('category')->orderBy('name')->get();
        $colors = Color::where('is_na', false)->orderBy('name')->get(); // Only grab actual colors

        return view('inventory.index', compact('inventories', 'types', 'colors'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-inventory');

        $request->validate([
            'consumable_type_id' => 'required|exists:consumable_types,id',
            'stock_level' => 'required|integer|min:0',
            'threshold' => 'required|integer|min:0',
        ]);

        // Find the type to check its category's color requirement
        $type = ConsumableType::with('category')->findOrFail($request->consumable_type_id);

        if ($type->category->requires_color) {
            $request->validate(['color_id' => 'required|exists:colors,id'], [
                'color_id.required' => 'A color must be selected for this category.'
            ]);
            $colorId = $request->color_id;
        } else {
            // If it doesn't require a color, force it to be null, ignoring any rogue form data
            $colorId = null;
        }

        // Check for duplicates before creating
        if (Inventory::where('consumable_type_id', $type->id)->where('color_id', $colorId)->exists()) {
            return back()->withErrors(['duplicate' => 'An inventory record for this Type and Color combination already exists.']);
        }

        Inventory::create([
            'consumable_type_id' => $type->id,
            'color_id' => $colorId,
            'stock_level' => $request->stock_level,
            'threshold' => $request->threshold,
        ]);

        return redirect()->route('inventory.index')->with('status', 'Inventory record added!');
    }

    public function destroy(Inventory $inventory)
    {
        Gate::authorize('manage-inventory');
        $inventory->delete();
        return redirect()->route('inventory.index')->with('status', 'Inventory record deleted.');
    }
}
