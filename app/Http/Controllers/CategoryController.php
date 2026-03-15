<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-assets');

        $request->validate(['name' => 'required|unique:categories|max:255']);

        Category::create([
            'name' => $request->name,
            'requires_color' => $request->has('requires_color') // Converts checkbox to boolean
        ]);

        return redirect()->route('categories.index')->with('status', 'Category added!');
    }

    public function destroy(Category $category)
    {
        Gate::authorize('manage-assets');
        $category->delete();
        return redirect()->route('categories.index')->with('status', 'Category deleted.');
    }
}
