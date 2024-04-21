<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    function index()
    {
        $content = About::first();
        return view('admin.about.index', compact('content'));
    }
    //============================================================
    function update(Request $request)
    {
        $request->validate([
            'content' => ['required']
        ]);

        About::updateOrCreate(
            ['id' => 1],
            [
                'content' => $request->content
            ]
        );
        toastr('Updated successfully!', 'success', 'success');
        return redirect()->back();
    }
}
