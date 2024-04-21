<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\CategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CategoryDataTable $dataTable, Request $request)
    {
        return $dataTable->render('admin.category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'icon' => ['required', 'not_in:empty'],
            'name' => ['required', 'max:200', 'unique:categories,name'],
            'status' => ['required']
        ]);
        $slug = Str::slug($request->name);
        $request->merge(['slug' => $slug]);
        Category::create($request->all());

        toastr('Created Successfully');
        return redirect()->route('admin.category.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'icon' => ['required', 'not_in:empty'],
            'name' => ['required', 'max:200', "unique:categories,name, $id"],
            'status' => ['required']
        ]);

        $category = Category::findOrFail($id);
        $category['slug'] = Str::slug($request->name);
        $category->update($request->all());

        toastr('updated Successfully');

        return redirect()->route('admin.category.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!isset($category)) {
            return response(['status' => 'error', 'message' => 'Can not delete, the item not found.']);
        }

        $subCategoryCount = SubCategory::where('category_id', $id)->count();
        if ($subCategoryCount > 0) {
            return response(['status' => 'error', 'message' => 'Can not delete, this category has sub categories.']);
        }

        $category->delete();
        toastr('Deleted Successfully');
        return response(['status' => 'success', 'message' => 'Deleted successfully']);
    }

    //change status using ajax request--------------------------------------------------
    public function changeStatus(Request $request)
    {
        $category = Category::findOrFail($request->id);

        $request->status == "true" ? $category->status = 'active' : $category->status = 'inactive';
        $category->save();

        return response(['message' => 'Status has been updated']);
    }
}
