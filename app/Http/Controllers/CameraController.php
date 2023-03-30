<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    public function insertCamera($cameraData) {
        if(isset($cameraData['camera_id'])) {
            $checkCamera = Camera::select('*')->where('camera_id', $cameraData['camera_id'])->first();
            if(!$checkCamera) {
                $camera = new Camera($cameraData);
                $camera->save();
                return $camera;
            }
        }
    }

    public function cameraList() {
        return Camera::select('*')->get();
    }

    public function findCamera($cameraId) {
        return Camera::select('*')->where('camera_id', $cameraId)->first();
    }
}
