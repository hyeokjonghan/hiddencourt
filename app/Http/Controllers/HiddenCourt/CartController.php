<?php

namespace App\Http\Controllers\HiddenCourt;

use App\Http\Controllers\Controller;
use App\Models\HiddenCourt\DevCart;
use Illuminate\Http\Request;


class CartController extends Controller
{
    public function getTodayReservation() {
        $startTimeStamp = strtotime(date("Y-m-d") . "-7 days");
        $date = date("Y-m-d",$startTimeStamp);
        $hour = (int) date("H");
        $hour = sprintf('%02d', ($hour - 1 + 24) % 24);
        return DevCart::select('*')->where('od_regdate', '>=', $date)
        ->where('first_time','<=', $hour.':00')
        ->where('od_status','<>','취소')
        ->where(function($q) {
            // 영상 정보 큐에 등록 되었는지 확인
            $q->where('is_convert_ready', false)
            ->orWhereNull('is_convert_ready');
        })
        ->where('od_use_machine', '<>', 'Y')
        ->orderBy('od_regdate','asc')
        ->take(6)
        ->get();
    }
}
