<?php

namespace App\Http\Controllers;

use App\Models\PrinterLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PrinterLocationController extends Controller
{
    public function index()
    {
        $locations = PrinterLocation::orderBy('name')->get();
        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'name' => 'required|unique:printer_locations|max:255',
            'description' => 'nullable|max:500',
        ]);

        PrinterLocation::create($request->all());

        return redirect()->route('locations.index')->with('status', 'Location created successfully!');
    }

    public function update(Request $request, PrinterLocation $location)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'name' => 'required|max:255|unique:printer_locations,name,' . $location->id,
            'description' => 'nullable|max:500',
        ]);

        $location->update($request->all());

        return redirect()->route('locations.index')->with('status', 'Location updated successfully!');
    }

    public function destroy(PrinterLocation $location)
    {
        Gate::authorize('manage-assets');

        $location->delete();
        return redirect()->route('locations.index')->with('status', 'Location deleted.');
    }
}
