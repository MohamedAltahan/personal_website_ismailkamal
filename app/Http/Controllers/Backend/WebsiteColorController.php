<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WebsiteColor;
use Illuminate\Http\Request;

class WebsiteColorController extends Controller
{
    public function update(Request $request)
    {
        $colors = WebsiteColor::first() ?? new WebsiteColor();

        $request->validate([
            'main_background' => ['max:7', 'min:7'],
            'secondary_background' => ['max:7', 'min:7'],
            'main_banner' => ['max:7', 'min:7'],
            'btn' => ['max:7', 'min:7'],
            'navbar' => ['max:7', 'min:7'],
            'text' => ['max:7', 'min:7'],
        ]);

        $colors->updateOrCreate(
            ['id' => $colors->id ?? 1],
            $request->all()
        );

        toastr('Saved Successfully');
        return redirect()->back();
    }
}
