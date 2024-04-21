<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FrontendSection;
use App\Models\HomePageSetting;
use Illuminate\Http\Request;

class HomePageSettingController extends Controller
{
    function index()
    {
        $productCategorySectionOne = HomePageSetting::where('key', 'products_slider_one')->first();
        $productCategorySectionTwo = HomePageSetting::where('key', 'products_slider_two')->first();
        $productCategorySectionThree = HomePageSetting::where('key', 'products_slider_three')->first();
        $categories = Category::where('status', 'active')->get();
        $popularCategorySection = HomePageSetting::where('key', 'popular_category_section')->first();

        $section = FrontendSection::first()->value;
        $sectionStatus = @json_decode($section);
        return view('admin.home-page-setting.index', compact(
            'productCategorySectionOne',
            'productCategorySectionTwo',
            'categories',
            'popularCategorySection',
            'productCategorySectionThree',
            'sectionStatus'
        ));
    }

    function updatePopularCategorySection(Request $request)
    {
        $request->validate(
            [
                'main_category_one' => ['required'],
                'main_category_two' => ['required'],
                'main_category_three' => ['required'],
                'main_category_four' => ['required'],
            ],
            [
                'main_category_one.required' => 'Category one field is required',
                'main_category_two.required' => 'Category two field is required',
                'main_category_three.required' => 'Category three field is required',
                'main_category_four.required' => 'Category four field is required',
            ]
        );
        $data = [
            [
                'main_category' => $request->main_category_one,
                'sub_category' => $request->sub_category_one,
                'child_category' => $request->child_category_one,
            ],
            [
                'main_category' => $request->main_category_two,
                'sub_category' => $request->sub_category_two,
                'child_category' => $request->child_category_two,
            ],
            [
                'main_category' => $request->main_category_three,
                'sub_category' => $request->sub_category_three,
                'child_category' => $request->child_category_three,
            ],
            [
                'main_category' => $request->main_category_four,
                'sub_category' => $request->sub_category_four,
                'child_category' => $request->child_category_four,
            ],
        ];

        HomePageSetting::updateOrCreate(
            ['key' => 'popular_category_section'],
            ['value' => json_encode($data)]
        );
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }
    //=======================================================
    function updateProductsSliderOne(Request $request)
    {
        $request->validate(
            ['main_category' => ['required']],
            ['main_category.required' => 'Category field is required']
        );

        $data = [
            'main_category' => $request->main_category,
            'sub_category' => $request->sub_category,
            'child_category' => $request->child_category,
        ];

        HomePageSetting::updateOrCreate(
            ['key' => 'products_slider_one'],
            ['value' => json_encode($data)]
        );
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }

    //===================================================================
    function updateProductsSliderTwo(Request $request)
    {
        $request->validate(
            ['main_category2' => ['required']],
            ['main_category2.required' => 'Category field is required']
        );

        $data = [
            'main_category' => $request->main_category2,
            'sub_category' => $request->sub_category2,
            'child_category' => $request->child_category2,
        ];

        HomePageSetting::updateOrCreate(
            ['key' => 'products_slider_two'],
            ['value' => json_encode($data)]
        );
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }

    //===================================================================
    function updateProductsSliderThree(Request $request)
    {
        $request->validate(
            ['main_category3' => ['required']],
            ['main_category3.required' => 'Category field is required']
        );

        $data = [
            'main_category' => $request->main_category3,
            'sub_category' => $request->sub_category3,
            'child_category' => $request->child_category3,
        ];

        HomePageSetting::updateOrCreate(
            ['key' => 'products_slider_three'],
            ['value' => json_encode($data)]
        );
        toastr('Updated successfully', 'success', 'success');
        return redirect()->back();
    }

    //change status using ajax request--------------------------------------------------
    public function changeStatus(Request $request)
    {
        $Sections = FrontendSection::first();
        $section = $Sections->value;
        $section = json_decode($section, true);

        $request->status == "true" ? $section[$request->sectionName] = 'active' : $section[$request->sectionName] = 'inactive';
        $section = json_encode($section);

        $Sections->update(['value' => $section]);

        return response(['message' => 'Status has been updated']);
    }
}
