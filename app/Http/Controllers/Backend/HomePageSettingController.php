<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\HomePageSetting;
use App\Models\Image;
use App\Models\Video;
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

    //============================================================
    function mediaOnHomePageUpdate(Request $request)
    {
        $request->validate([
            'image.*' => ['image'],
            'video.*' => ['mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'],
        ]);


        if ($request->has('video')) {
            $videos = $this->filesUplaod($request, 'myDisk', 'videos', 'video');
            foreach ($videos as $video) {
                Video::create([
                    'design_id' => null,
                    'name' => $video,
                    'at_home' => 'yes'
                ]);
            }
        }

        if ($request->has('image')) {
            $images = $this->filesUplaod($request, 'myDisk', 'images', 'image');
            foreach ($images as $image) {
                Image::create([
                    'design_id' => null,
                    'name' => $image,
                    'at_home' => 'yes'
                ]);
            }
        }


        toastr('Added successfully');
        return redirect()->back();
    }
}
