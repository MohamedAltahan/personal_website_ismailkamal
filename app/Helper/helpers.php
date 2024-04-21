<?php
//set sidebar acive

use App\Models\Setting;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

function setActive(array $routes)
{
    if (is_array($routes)) {
        foreach ($routes as $route) {
            //check the current request if like $route
            if (request()->routeIs($route)) {
                return 'active';
            }
        }
    }
}

//product has valid discount - check start and end date=============================
function checkDiscount($product)
{
    $currentDate = date('Y-m-d');

    if (isset($product->offer_price)) {
        //if date fields are null means that product offer is valid forever
        if ($product->offer_start_date == null &&  $product->offer_end_date == null) {
            return true;
        }
        if ($currentDate >= $product->offer_start_date && $currentDate <= $product->offer_end_date) {
            return true;
        }
    }
    return false;
}
//discount percentage depend on original price and discount price================
function calcDiscountPercentage($originalPrice, $discountPrice)
{
    $discountAmount = $originalPrice - $discountPrice;
    return ceil(($discountAmount / $originalPrice) * 100);
}

//total for whole cart before discount===========================================
function calcCartTotal()
{
    $cartProducts = Cart::content();
    $totalCartPrice = 0;
    foreach ($cartProducts as $product) {
        $totalCartPrice +=  ($product->qty * $product->price);
    }
    return $totalCartPrice;
}

// get total amount  after coupone discount ===============================================
function getMainCartTotal()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = calcCartTotal();
        if ($coupon['discount_type'] == 'fixed') {
            $total = $subTotal - $coupon['discount_value'];
            return $total;
        } elseif ($coupon['discount_type'] == 'percent') {
            $discount =  $subTotal * $coupon['discount_value'] / 100;
            $total = $subTotal - $discount;
            return  $total;
        }
    } else {
        return calcCartTotal();
    }
}

// get coupone discount ================================================================
function getMainCartDiscount()
{
    if (Session::has('coupon')) {
        $coupon = Session::get('coupon');
        $subTotal = calcCartTotal();
        if ($coupon['discount_type'] == 'fixed') {
            return $coupon['discount_value'];
        } elseif ($coupon['discount_type'] == 'percent') {
            $discount =  $subTotal * $coupon['discount_value'] / 100;
            return $discount;
        }
    } else {
        return 0;
    }
}

//get shipping fee======================================================
function getShippingFee()
{
    if (Session::has('shipping_method')) {
        return Session::get('shipping_method')['cost'];
    } else {
        return 0;
    }
}

//get final payment amount(total+coupon+shipping fee)

function  finalPaymentAmount()
{
    return getMainCartTotal() + getShippingFee();
}

//limit text
function limitText($text, $limit = 20)
{
    return Str::limit($text, $limit);
}

//limit text
function getCurrency()
{
    return Setting::first()->currency;
}
