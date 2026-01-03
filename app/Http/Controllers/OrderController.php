<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderProduct;
class OrderController extends Controller
{
    public function history()
    {
       $orders = OrderProduct::latest()->get();
        return view('user.riwayat', compact('orders'));
    }
}
