<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\SubCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.sub-category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    { //empty object used to fill inputs will empty values when create new subCategory
        $subCategory = new SubCategory();
        $categories = Category::all();
        return view('admin.sub-category.create', compact('categories', 'subCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required'],
            'name' => ['required', 'max:200'],
            'status' => ['required'],
        ]);

        $request['slug'] = Str::slug($request->name);
        SubCategory::create($request->all());
        toastr('Created Successfully');
        return redirect()->route('admin.sub-category.index');
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

        $subCategory = SubCategory::findOrFail($id);
        $categories = Category::all();
        return view('admin.sub-category.edit', compact('categories', 'subCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'category_id' => ['required'],
            'name' => ['required', 'max:200'],
            'status' => ['required'],
        ]);

        $subCategory = SubCategory::findOrFail($id);
        $subCategory['slug'] = Str::slug($request->name);
        $subCategory->update($request->all());
        toastr('Created Successfully');
        return redirect()->route('admin.sub-category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subCategory = SubCategory::find($id);

        if (!isset($subCategory)) {
            return response(['status' => 'error', 'message' => 'Can not delete, the item not found.']);
        }

        $ChildCategoryCount = ChildCategory::where('sub_category_id', $id)->count();
        if ($ChildCategoryCount > 0) {
            return response(['status' => 'error', 'message' => 'Can not delete, this sub category has child categories.']);
        }

        $subCategory->delete();
        toastr('Deleted Successfully');
        return response(['status' => 'success', 'message' => 'Deleted sucessfully']);
    }

    //change status using ajax request--------------------------------------------------
    public function changeStatus(Request $request)
    {
        $subCategory = SubCategory::findOrFail($request->id);

        $request->status == "true" ? $subCategory->status = 'active' : $subCategory->status = 'inactive';
        $subCategory->save();

        return response(['message' => 'Status has been updated']);
    }
}
