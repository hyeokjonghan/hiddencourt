<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Lib\CURLController;
use Illuminate\Http\Request;

class ktApiController extends Controller
{
    public function generateAuthTicket() {
        $result = shell_exec('python '.app_path().'/AuthTicket/CertGenerator.py');
        return $result;
    }

    // auth_token값 갱신
    public function getAuthToken()
    {
        $uri = env("GIGA_API_URL")."/gigaeyes/v1.0/authToken";
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic '.base64_encode(env('GIGA_ID').':'.env('GIGA_PASS'))
        ];


        $body = [
            'request'=>[
                'auth_ticket'=>$this->generateAuthTicket(),
                'offset_position'=>700,
                'offset_length'=>128,
                'site_id'=>env('GIGA_SITEID')
            ]
        ];

        $body = json_encode($body,true);

        
        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri,$body,$header);
        
        $returnData = json_decode($returnData['data'],true);

        if($returnData['returndescription'] == "Success") {
            $authToken = $returnData['response']['auth_token'];
            return $authToken;
        } else {
            return false;
        }
    }

    // camera list
    public function camera() {
        $uri = env("GIGA_API_URL")."/gigaeyes/v1.0/camera";
        $authToken = $this->getAuthToken();
        if($authToken) {
            $header = [
                'Content-Type:application/json;charset=UTF-8',
                'Authorization:Basic '.base64_encode(env('GIGA_ID').':'.env('GIGA_PASS')),
                'User-Agent:GiGAeyes (compatible;DeviceType/iPhone;DeviceModel/SCH-M20;DeviceId/3F2A009CDE;OSType/iOS;OSVersion/5.1.1;AppVersion/3.0.0;IpAddr/'.env('SERVER_IP').')',
                'authToken:'.$authToken
            ];
            $body = [
                'request'=>[
                    'account_id'=>'0024200744'  // Test Data
                ]
            ];
            $body = json_encode($body, true);
    
            $curlController = new CURLController();
            $returnData = $curlController->postCURL($uri, $body, $header);
            $returnData = json_decode($returnData['data'], true);
            return $returnData;
        } else {
            return false;
        }
    }

    // 영상 정보 가져오기
    public function recordVideo($authToken, $camId, $startTime='', $endTime='') {
        $uri = env("GIGA_API_URL")."/gigaeyes/v1.0/recordVideo";
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic '.base64_encode(env('GIGA_ID').':'.env('GIGA_PASS')),
            'User-Agent:GiGA Eyes(compatible;DeviceType/PC;DeviceMode/PC;DeviceId/469F03EC8E35E3371CADF016F93BE670;OSType/PC;OSVersion/1.0;AppVersion/3.4.12;IpAddr/' . env('SERVER_IP') . ')',
            'authToken:'.$authToken
        ];

        $body = [
            'request'=>[
                'cam_id'=>$camId,
                'start_time'=>$startTime,
                'end_time'=>$endTime
            ]
        ];

        //yyyymmddhhmmss

        $body = json_encode($body, true);
        
        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri, $body, $header);
        $returnData = json_decode($returnData['data'], true);
        return $returnData;
    }
}
