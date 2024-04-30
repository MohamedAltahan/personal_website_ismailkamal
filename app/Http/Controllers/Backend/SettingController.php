<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\HomePageSetting;
use App\Models\Image;
use App\Models\LogoSetting;
use App\Models\Setting;
use App\Models\Video;
use App\Models\WebsiteColor;
use App\Traits\fileUploadTrait;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use fileUploadTrait;
    //________________________________________________________________
    public function index()
    {
        $setting = Setting::first();
        $logoSetting = LogoSetting::first();
        $content = About::first();
        $homePage = HomePageSetting::first();
        $videos = Video::where('at_home', 'yes')->get();
        $images = Image::where('at_home', 'yes')->get();
        $color = WebsiteColor::first();
        return view('admin.setting.index', compact(
            'setting',
            'logoSetting',
            'content',
            'homePage',
            'videos',
            'images',
            'color'
        ));
    }

    //__________________________________________________________________
    public function generalSettingUpdate(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'max:200'],
            'contact_email' => ['required', 'max:200'],
            'contact_phone' => ['max:50'],
            'contact_address' => ['max:250'],
        ]);

        Setting::updateOrCreate(['id' => 1], $request->all());
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }


    //________________________________________________________________
    function logoSettingUpdate(Request $request)
    {
        $request->validate([
            'main_logo' => ['image', 'max:3000'],
            'icon' => ['image', 'max:3000'],
        ]);

        $oldLogos = LogoSetting::first() ?: new LogoSetting();

        $logos = [];

        if ($request->hasFile('main_logo')) {
            $oldMainLogo = $oldLogos['main_logo'];
            $logos['main_logo'] = $this->fileUpdate($request, 'myDisk', 'websiteLogo', 'main_logo', $oldMainLogo);
        }

        if ($request->hasFile('icon')) {
            $oldIcon = $oldLogos['icon'];
            $logos['icon'] = $this->fileUpdate($request, 'myDisk', 'websiteLogo', 'icon', $oldIcon);
        }

        LogoSetting::updateOrCreate(
            ['id' => $oldLogos->id ?? 1],
            $logos
        );
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }
}
