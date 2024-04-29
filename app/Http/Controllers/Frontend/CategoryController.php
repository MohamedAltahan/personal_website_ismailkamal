<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Design;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($id)
    {
        $designs = Design::with('category')->where('category_id', $id)->get();
        $categoryName = Category::findOrFail($id)->name;
        return view('frontend.pages.show-designs', compact('designs', 'categoryName'));
    }
}
