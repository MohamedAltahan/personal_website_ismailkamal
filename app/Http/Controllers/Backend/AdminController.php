<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Review;

class AdminController extends Controller
{
    public function dashboard()
    {

        return view('admin.dashboard');
    }


    public function login()
    {

        return view('admin.auth.login');
    }
}
