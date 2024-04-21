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
        $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
        $totalOrders = Order::count();
        $todayPendingOrders = Order::whereDate('created_at', Carbon::today())
            ->where('order_status', 'pending')
            ->count();
        $allPendingOrders = Order::where('order_status', 'pending')->count();
        $allCanceledOrders = Order::where('order_status', 'canceled')->count();
        $allCompletedOrders = Order::where('order_status', 'delivered')->count();
        $todaySales = Order::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'completed')
            ->where('order_status', '!=', 'canceled')
            ->sum('sub_total');
        $thisMonthSales = Order::whereMonth('created_at', Carbon::today()->month)
            ->where('payment_status', 'completed')
            ->where('order_status', '!=', 'canceled')
            ->sum('sub_total');
        $thisYearSales = Order::whereYear('created_at', Carbon::today()->year)
            ->where('payment_status', 'completed')
            ->where('order_status', '!=', 'canceled')
            ->sum('sub_total');
        $allReviews = ProductReview::count();
        $allUsers = User::where('role', 'user')->count();
        $allVendors = User::where('role', 'vendor')->count();
        return view('admin.dashboard', compact(
            'todayOrders',
            'todayPendingOrders',
            'totalOrders',
            'allPendingOrders',
            'allCanceledOrders',
            'allCompletedOrders',
            'todaySales',
            'thisMonthSales',
            'thisYearSales',
            'allReviews',
            'allUsers',
            'allVendors'
        ));
    }


    public function login()
    {

        return view('admin.auth.login');
    }
}
