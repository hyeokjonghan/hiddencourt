<?php

namespace App\Http\Controllers\HiddenCourt;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ktApiController;
use App\Http\Controllers\UploadController;
use App\Jobs\ConvertMovie;
use App\Models\Camera;
use App\Models\HiddenCourt\DevCart;
use App\Models\HiddenCourt\DevClip;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ClipController extends Controller
{
    public function getClipList()
    {
        return DevClip::select('*')->get();
    }

    public function setClipToday()
    {
        Log::info('INIT SET CLIP TODAY');
        $cartController = new CartController();
        $todayCartList = $cartController->getTodayReservation();
        $ktApiController = new ktApiController();
        $authToken = $ktApiController->getAuthToken();
        Log::info('SET CLIP AUTH TOKEN CHECK ==>');
        Log::info($authToken);
        if ($authToken) {
            Log::info('INIT SET CLIP CHECK AUTH');
            foreach ($todayCartList as $todayCart) {
                $coatName = str_replace(' ', '', $todayCart['coatname']);
                $cameraInfo = Camera::select('*')->where('cam_name', $coatName)->first();

                if ($todayCart['first_time']) {
                    $checkQuery = DevClip::select('*')->where('cart_time', 1)->where('cart_idx', $todayCart['idx'])->get();
                    if (count($checkQuery) == 0) {
                        $this->saveNewClip($authToken, $todayCart, $todayCart['first_time'], 1, $cameraInfo);
                    }
                }

                if ($todayCart['second_time']) {
                    $checkQuery = DevClip::select('*')->where('cart_time', 2)->where('cart_idx', $todayCart['idx'])->get();
                    if (count($checkQuery) == 0) {
                        $this->saveNewClip($authToken, $todayCart, $todayCart['second_time'], 2, $cameraInfo);
                    }
                }
            }
        }
    }

    public function saveNewClip($authToken, $cartInfo, $time, $cartTime, $cameraInfo)
    {
        $ktApiController = new ktApiController();
        $startTime = str_replace('-','',$cartInfo['od_regdate']) . str_replace(':', '', $time) . '00';
        $startTimeStamp = strtotime($cartInfo['od_regdate'] . " " . $time . ":00" . "+30 minutes");
        $endTime = date("YmdHis", $startTimeStamp);
        $videoInfo = $ktApiController->recordVideo($authToken, $cameraInfo['camera_id'], $startTime, $endTime);
        Log::info('SAVE NEW CLIP ==>');
        Log::info($videoInfo);
        // 영상 정보가 있을 경우
        if (isset($videoInfo['response']['stream_url'])) {

            $context = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $html = file_get_contents($videoInfo['response']['stream_url'], false, stream_context_create($context));

            
            $filePath = "common/video/".$cartInfo['phoneid'].'/'.uniqid().'.m3u8';
            Storage::disk('s3')->put($filePath, $html);

            // 해당 부분 부터 큐로 처리
            Queue::push(new ConvertMovie($filePath, $cartInfo, $cartTime));

        }
    }

    public function clipSync(Request $request)
    {
        
        $validator = [
            'idx' => 'required|integer',
            'phoneid' => 'required|string'
        ];

        $validatorCheck = Validator::make($request->all(), $validator);
        if ($validatorCheck->fails()) {
            return response($validatorCheck->errors()->all(), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $cartInfo = DevCart::select('*')->where('idx', $request->idx)->where('phoneid', $request->phoneid)->first();
        if(!$cartInfo) {
            return response()->caps('cart information is not found.', Response::HTTP_NOT_FOUND);
        }

        $coatName = str_replace(' ', '', $cartInfo['coatname']);
        $cameraInfo = Camera::select('*')->where('cam_name', $coatName)->first();
        if(!$cameraInfo) {
            return response()->caps('camera information is not found.', Response::HTTP_NOT_FOUND);
        }

        $ktApiController = new ktApiController();
        $authToken = $ktApiController->getAuthToken();
        if(!$authToken) {
            return response()->caps('KT GIGAEYES API ERROR.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        try{
            if ($cartInfo['first_time']) {
                $checkQuery = DevClip::select('*')->where('cart_time', 1)->where('cart_idx', $cartInfo['idx'])->get();
                if (count($checkQuery) == 0) {
                    $this->saveNewClip($authToken, $cartInfo, $cartInfo['first_time'], 1, $cameraInfo);
                }
            }
    
            if ($cartInfo['second_time']) {
                $checkQuery = DevClip::select('*')->where('cart_time', 2)->where('cart_idx', $cartInfo['idx'])->get();
                if (count($checkQuery) == 0) {
                    $this->saveNewClip($authToken, $cartInfo, $cartInfo['second_time'], 2, $cameraInfo);
                }
            }
            
        } catch(Error $e) {
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return DevClip::select('*')->where('cart_idx', $cartInfo['idx'])->get();
    }
}
