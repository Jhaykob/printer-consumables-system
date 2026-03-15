<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        return view('departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate([
            'name' => 'required|unique:departments|max:255',
        ]);

        Department::create($request->all());

        return redirect()->route('departments.index')->with('status', 'Department created successfully!');
    }

    public function destroy(Department $department)
    {
        Gate::authorize('manage-assets');

        $department->delete();
        return redirect()->route('departments.index')->with('status', 'Department deleted.');
    }
}
