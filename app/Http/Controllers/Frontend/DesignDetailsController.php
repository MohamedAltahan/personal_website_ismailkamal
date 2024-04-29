<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignDetailsController extends Controller
{
    public function index($id)
    {
        $design = Design::with('videos', 'images')->findOrFail($id);
        return view('frontend.pages.show-design-details', compact('design'));
    }
}
