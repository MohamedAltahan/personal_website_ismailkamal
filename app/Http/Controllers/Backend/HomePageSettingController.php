<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting;
use App\Traits\fileUploadTrait;
use Illuminate\Http\Request;

class HomePageSettingController extends Controller
{
    use fileUploadTrait;
    //============================================================
    function update(Request $request)
    {
        $request->validate([
            'image' => ['image'],
            'main_title' => ['max:2000'],
            'description' => ['max:2000']
        ]);

        $setting = $request->except('image');
        if ($request->has('image')) {
            $setting['image'] = $this->fileUplaod($request, 'myDisk', 'homePageImage', 'image');
        }

        HomePageSetting::updateOrCreate(
            ['id' => 1],
            $setting
        );
        toastr('Updated successfully!', 'success', 'success');
        return redirect()->back();
    }
}
