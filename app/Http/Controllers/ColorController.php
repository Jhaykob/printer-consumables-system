<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::orderBy('name')->get();
        return view('colors.index', compact('colors'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate(['name' => 'required|unique:colors|max:255']);

        // Prevent creating multiple "N/A" colors
        if ($request->has('is_na') && Color::where('is_na', true)->exists()) {
            return back()->withErrors(['is_na' => 'An N/A color already exists.']);
        }

        Color::create([
            'name' => $request->name,
            'is_na' => $request->has('is_na')
        ]);

        return redirect()->route('colors.index')->with('status', 'Color added!');
    }

    public function destroy(Color $color)
    {
        Gate::authorize('manage-assets');

        if ($color->is_na) {
            return back()->withErrors(['delete' => 'Cannot delete the base N/A color.']);
        }

        $color->delete();
        return redirect()->route('colors.index')->with('status', 'Color deleted.');
    }
}
