<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Traits\fileUploadTrait;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    use fileUploadTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $footerInfo = Footer::first();
        $footerInfo = $footerInfo ? $footerInfo : new Footer();
        return view('admin.footer.footer-info.index', compact('footerInfo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'max:300'],
            'phone' => ['max:50'],
            'email' => ['max:50'],
            'address' => ['max:400'],
            'copyright' => ['max:300'],

        ]);

        $footer = $request->except('logo');

        $footerInfo = Footer::find($id);

        if ($request->has('logo')) {
            $oldLogoPath = @$footerInfo->logo;
            $footer['logo'] = $this->fileUpdate($request, 'myDisk', 'footer', 'logo', $oldLogoPath);
        }

        Footer::updateOrCreate(['id' => $id], $footer);
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
