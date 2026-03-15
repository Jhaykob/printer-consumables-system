<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\PrinterLocation;
use App\Models\ConsumableType;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PrinterController extends Controller
{
    public function index()
    {
        // Eager load the new department relationship
        $printers = Printer::with(['department', 'location', 'consumableTypes'])->orderBy('name')->get();
        $locations = PrinterLocation::orderBy('name')->get();
        $departments = Department::orderBy('name')->get(); // <-- Add this
        $consumableTypes = ConsumableType::with('category')->orderBy('name')->get();

        // Pass $departments to the view
        return view('printers.index', compact('printers', 'locations', 'departments', 'consumableTypes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'printer_location_id' => 'required|exists:printer_locations,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:printers|max:255',
            'ip_address' => 'nullable|ip',
            'consumable_types' => 'nullable|array', // Validate the checkboxes
            'consumable_types.*' => 'exists:consumable_types,id'
        ]);

        $printer = Printer::create($request->except('consumable_types'));

        // Sync the checkboxes to the pivot table
        if ($request->has('consumable_types')) {
            $printer->consumableTypes()->sync($request->consumable_types);
        }

        return redirect()->route('printers.index')->with('status', 'Printer added successfully!');
    }

    public function update(Request $request, Printer $printer)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'printer_location_id' => 'required|exists:printer_locations,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|max:255|unique:printers,serial_number,' . $printer->id,
            'ip_address' => 'nullable|ip',
        ]);

        $printer->update($request->all());

        return redirect()->route('printers.index')->with('status', 'Printer updated successfully!');

        $printer->consumableTypes()->sync($request->consumable_types);
    }

    public function destroy(Printer $printer)
    {
        Gate::authorize('manage-assets');
        $printer->delete();
        return redirect()->route('printers.index')->with('status', 'Printer removed.');
    }
}
