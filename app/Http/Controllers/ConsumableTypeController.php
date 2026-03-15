<?php

namespace App\Http\Controllers;

use App\Models\ConsumableType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConsumableTypeController extends Controller
{
    public function index()
    {
        // Eager load the category to prevent N+1 query performance issues
        $types = ConsumableType::with('category')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('consumable-types.index', compact('types', 'categories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        ConsumableType::create($request->all());

        return redirect()->route('consumable-types.index')->with('status', 'Consumable Type added!');
    }

    public function destroy(ConsumableType $consumable_type)
    {
        Gate::authorize('manage-assets');

        $consumable_type->delete();
        return redirect()->route('consumable-types.index')->with('status', 'Consumable Type deleted.');
    }
}
