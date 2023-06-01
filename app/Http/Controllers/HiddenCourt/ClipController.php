<?php

namespace App\Http\Controllers\HiddenCourt;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ktApiController;
use App\Models\Camera;
use App\Models\HiddenCourt\DevClip;
use Illuminate\Http\Request;

class ClipController extends Controller
{
    public function getClipList()
    {
        return DevClip::select('*')->get();
    }

    public function setClipToday()
    {
        $cartController = new CartController();
        $todayCartList = $cartController->getTodayReservation();
        $ktApiController = new ktApiController();
        $authToken = $ktApiController->getAuthToken();
        if ($authToken) {
            foreach ($todayCartList as $todayCart) {
                $coatName = str_replace(' ', '', $todayCart['coatname']);
                    $cameraInfo = Camera::select('*')->where('cam_name', $coatName)->first();
                    
                    if ($todayCart['first_time']) {
                        $checkQuery = DevClip::select('*')->where('cart_time',1)->where('cart_idx', $todayCart['idx'])->get();
                        if(count($checkQuery) == 0) {
                            $this->saveNewClip($authToken, $todayCart, $todayCart['first_time'], 1, $cameraInfo);
                        }
                    }

                    if($todayCart['second_time']) {
                        $checkQuery = DevClip::select('*')->where('cart_time',2)->where('cart_idx', $todayCart['idx'])->get();
                        if(count($checkQuery) == 0) {
                            $this->saveNewClip($authToken, $todayCart, $todayCart['second_time'], 2, $cameraInfo);
                        }
                    }
            }
            
        }
    }

    public function saveNewClip($authToken, $cartInfo, $time, $cartTime, $cameraInfo)
    {
        $ktApiController = new ktApiController();
        $startTime = date("Ymd") . str_replace(':', '', $time) . '00';
        $startTimeStamp = strtotime(date("Y-m-d") . " " . $time . ":00" . "+30 minutes");
        $endTime = date("YmdHis", $startTimeStamp);
        $videoInfo = $ktApiController->recordVideo($authToken, $cameraInfo['camera_id'], $startTime, $endTime);

        // 영상 정보가 있을 경우
        if (isset($videoInfo['response']['stream_url'])) {
            $clip = new DevClip([
                'cart_idx' => $cartInfo['idx'],
                'phoneid' => $cartInfo['phoneid'],
                'link' => $videoInfo['response']['stream_url'],
                'cart_time'=>$cartTime,
                'regdate' => date("Y-m-d H:i:s"),
                'limitdate' => date("Y-m-d", strtotime(date("Y-m-d") . "+7 days"))
            ]);
            $clip->save();
        }
    }
}
