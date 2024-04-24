<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\SocialDataTable;
use App\Http\Controllers\Controller;
use App\Models\Social;
use Illuminate\Http\Request;

class SocialController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(SocialDataTable $dataTable)
    {
        return $dataTable->render('admin.socials.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.socials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'icon' => ['required', 'max:200'],
            'name' => ['required', 'max:200'],
            'link' => ['required', 'url'],
            'status' => ['in:active,inactive'],
        ]);

        $footerButtons = new Social();
        $footerButtons->create($request->all());
        toastr('Crated successfully', 'success', 'success');
        return redirect()->route('admin.socials.index');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $footerButtonInfo = Social::findOrFail($id);
        return view('admin.socials.edit', compact('footerButtonInfo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'icon' => ['required', 'max:200'],
            'name' => ['required', 'max:200'],
            'link' => ['required', 'url'],
            'status' => ['in:active,inactive'],
        ]);

        $footerButtons =  Social::findOrFail($id);
        $footerButtons->update($request->all());
        toastr('Updated successfully', 'success', 'success');
        return redirect()->route('admin.socials.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $footerButtons =  Social::findOrFail($id);
        $footerButtons->delete();

        return response(['status' => 'success', 'message' => 'Deleted successfully']);
    }

    //change status using ajax request--------------------------------------------------
    public function changeStatus(Request $request)
    {
        $category = Social::findOrFail($request->id);

        $request->status == "true" ? $category->status = 'active' : $category->status = 'inactive';
        $category->save();

        return response(['message' => 'Status has been updated']);
    }
}
