<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    public function cameraList() {
        return Camera::select('*')->get();
    }

    public function findCamera($cameraId) {
        return Camera::select('*')->where('camera_id', $cameraId)->first();
    }

    public function insertCameraInit(Request $request) {
        $ktApiController = new ktApiController();
        $cameraList = $ktApiController->camera();
        if(isset($cameraList["response"]["list"])) {
            $cameraList = $cameraList["response"]["list"];
            foreach($cameraList as $camera) {
                
                $checkQuery = Camera::select('*')->where('camera_id', $camera['cam_id'])->get();
                if(count($checkQuery) == 0) {
                    $camera = new Camera([
                        'camera_id'=>$camera['cam_id'],
                        'cam_name'=>$camera['cam_name'],
                        'mac_id'=>$camera['mac_id'],
                        'serial_number'=>$camera['serial_num'],
                        'cam_group_id'=>$camera['cam_group_id'],
                        'model_name'=>$camera['model_name'],
                        'cam_firmware'=>$camera['cam_firmware'],
                        'cam_group_name'=>$camera['cam_group_name']
                    ]);
    
                    $camera->save();
                }
            }
            return Camera::select('*')->get();
        } else {
            return false;
        }

    }
}
