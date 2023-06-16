<?php

namespace App\Http\Controllers\HiddenCourt;

use App\Http\Controllers\Controller;
use App\Models\HiddenCourt\DevCart;
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function getTodayReservation() {
        $date = date("Y-m-d");
        // $startTimeStamp = strtotime(date("Y-m-d") . "-1 days");
        // $date = date("Y-m-d",$startTimeStamp);
        return DevCart::select('*')->where('od_regdate', $date)->where('od_status','<>','취소')->get();
    }
}
