<?php

namespace App\Http\Controllers;

use App\Models\Printer;
use App\Models\PrinterLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PrinterController extends Controller
{
    public function index()
    {
        // 'with' uses Eager Loading to grab the location name efficiently
        $printers = Printer::with('location')->orderBy('name')->get();
        $locations = PrinterLocation::orderBy('name')->get(); // For the dropdown

        return view('printers.index', compact('printers', 'locations'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'printer_location_id' => 'required|exists:printer_locations,id',
            'name' => 'required|string|max:255',
            'serial_number' => 'required|string|unique:printers|max:255',
            'ip_address' => 'nullable|ip',
        ]);

        Printer::create($request->all());

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
    }

    public function destroy(Printer $printer)
    {
        Gate::authorize('manage-assets');

        $printer->delete();
        return redirect()->route('printers.index')->with('status', 'Printer removed.');
    }
}
