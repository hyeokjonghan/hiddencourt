<?php

namespace App\Http\Controllers\CCTV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Lib\CURLController;
use App\Http\Controllers\UploadController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Storage;

class CCTVController extends Controller
{

    // F00242007441001, F00242007441002, F00242007441003, F00242007441004 ==> CAMERA LIST

    // 카메라 목록 호출 (테스트 완료, 추후 데이터 수정만 해주면 됨)
    public function camera(Request $request)
    {
        $uri = env("GIGA_API_URL") . "/gigaeyes/v1.0/camera";
        $authToken = $request->user()['giga_auth_token'];
        // $authToken = "ZGF0YT1kNjRjMTQ5ZDkwMDgyN2RkOWJjMjZjMzQyYTk3OWVlMmNhMDZjMDQzYzk1N2U5NzMyNTg2OTliODc5YmRiZDMzOTVlYzg1MmE5OTk2NzNlNGIyNzI4ZDRhNTAyOGNlODI2M2IyYTgxOWFlYzNmNDUyZTEzODFkZDRlNDNjNDdiZWVmMGViZTZjZjc4MzU1N2U0YTEyNDIyMGQyNmMwNjZmMmVhMTM5ODZlZDY5MDY4OTQxNmI0NzVlYTY0OWExODJlMGZkYmM2N2JhYjBiNjA5ZWFmOWRlNWFmNWE1YjRiMTczODAxZTJiYWU3YmQ5N2ViNzA0OWViM2Q0MTdjNGZjMjEwZGIwODAzZDE3MmNhZDVjMDBhNWMzNjA0ZjE0ZWQ3MDdiODY0OWVjOTdiYTU5MGM1OWRjZjJlZmQ3YTgwM2RmOTcwNWEyMDJjN2ExNTM1NDNlNzY3N2JiNzI3NDcwMjU5NTAzOTBkYmVmOTVjMzFmZWEwZTU2NjBhOWQwZGImbG9naW5LZXk9MzFiNTA2YTItYmZiZS00ZTk0LTk0YTMtMGM1ZWRkMWQ5YzBj";
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic ' . base64_encode(env('GIGA_ID') . ':' . env('GIGA_PASS')),
            'User-Agent:GiGA Eyes(compatible;DeviceType/PC;DeviceMode/PC;DeviceId/469F03EC8E35E3371CADF016F93BE670;OSType/PC;OSVersion/1.0;AppVersion/3.4.12;IpAddr/' . env('SERVER_IP') . ')',
            'authToken:' . $authToken
        ];
        $body = [
            'request' => [
                'account_id' => '0024200744'  // Test Data
            ]
        ];
        $body = json_encode($body, true);

        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri, $body, $header);
        $returnData = json_decode($returnData['data'], true);
        return $returnData;
    }

    // 레코드 비디오 목록
    public function recordVideo(Request $request)
    {
        $uri = env("GIGA_API_URL") . "/gigaeyes/v1.0/recordVideo";
        $authToken = $request->user()['giga_auth_token'];
        // $authToken = "ZGF0YT1kNjRjMTQ5ZDkwMDgyN2RkOWJjMjZjMzQyYTk3OWVlMmNhMDZjMDQzYzk1N2U5NzMyNTg2OTliODc5YmRiZDMzOTVlYzg1MmE5OTk2NzNlNGIyNzI4ZDRhNTAyOGNlODI2M2IyYTgxOWFlYzNmNDUyZTEzODFkZDRlNDNjNDdiZWVmMGViZTZjZjc4MzU1N2U0YTEyNDIyMGQyNmMwNjZmMmVhMTM5ODZlZDY5MDY4OTQxNmI0NzVlYTY0OWExODJlMGZkYmM2N2JhYjBiNjA5ZWFmOWRlNWFmNWE1YjRiMTczODAxZTJiYWU3YmQ5N2ViNzA0OWViM2Q0MTdjNGZjMjEwZGIwODAzZDE3MmNhZDVjMDBhNWMzNjA0ZjE0ZWQ3MDdiODY0OWVjOTdiYTU5MGM1OWRjZjJlZmQ3YTgwM2RmOTcwNWEyMDJjN2ExNTM1NDNlNzY3N2JiNzI3NDcwMjU5NTAzOTBkYmVmOTVjMzFmZWEwZTU2NjBhOWQwZGImbG9naW5LZXk9MzFiNTA2YTItYmZiZS00ZTk0LTk0YTMtMGM1ZWRkMWQ5YzBj";
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic ' . base64_encode(env('GIGA_ID') . ':' . env('GIGA_PASS')),
            'User-Agent:GiGA Eyes(compatible;DeviceType/PC;DeviceMode/PC;DeviceId/469F03EC8E35E3371CADF016F93BE670;OSType/PC;OSVersion/1.0;AppVersion/3.4.12;IpAddr/' . env('SERVER_IP') . ')',
            'authToken:' . $authToken
        ];

        $body = [
            'request' => [
                'cam_id' => 'F00242007441001',
                'start_time' => '20230610101000',
                'end_time' => '20230610101500'
            ]
        ];

        //yyyymmddhhmmss

        $body = json_encode($body, true);




        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri, $body, $header);
        $returnData = json_decode($returnData['data'], true);

        // TEST returnData->response->stream_url
        if (isset($returnData['response']['stream_url'])) {
            $context = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $html = file_get_contents($returnData['response']['stream_url'], false, stream_context_create($context));
            Storage::disk('s3')->put('test.m3u8', $html);
            

            // 해당 파일을 S3에 업로드
            

            return $html;
        }

        return $returnData;
    }

    public function recordVideoList(Request $request)
    {
        $uri = env("GIGA_API_URL") . "/gigaeyes/v1.0/recordVideoList";
        $authToken = $request->user()['giga_auth_token'];
        // $authToken = "ZGF0YT1kNjRjMTQ5ZDkwMDgyN2RkOWJjMjZjMzQyYTk3OWVlMmNhMDZjMDQzYzk1N2U5NzMyNTg2OTliODc5YmRiZDMzOTVlYzg1MmE5OTk2NzNlNGIyNzI4ZDRhNTAyOGNlODI2M2IyYTgxOWFlYzNmNDUyZTEzODFkZDRlNDNjNDdiZWVmMGViZTZjZjc4MzU1N2U0YTEyNDIyMGQyNmMwNjZmMmVhMTM5ODZlZDY5MDY4OTQxNmI0NzVlYTY0OWExODJlMGZkYmM2N2JhYjBiNjA5ZWFmOWRlNWFmNWE1YjRiMTczODAxZTJiYWU3YmQ5N2ViNzA0OWViM2Q0MTdjNGZjMjEwZGIwODAzZDE3MmNhZDVjMDBhNWMzNjA0ZjE0ZWQ3MDdiODY0OWVjOTdiYTU5MGM1OWRjZjJlZmQ3YTgwM2RmOTcwNWEyMDJjN2ExNTM1NDNlNzY3N2JiNzI3NDcwMjU5NTAzOTBkYmVmOTVjMzFmZWEwZTU2NjBhOWQwZGImbG9naW5LZXk9MzFiNTA2YTItYmZiZS00ZTk0LTk0YTMtMGM1ZWRkMWQ5YzBj";
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic ' . base64_encode(env('GIGA_ID') . ':' . env('GIGA_PASS')),
            'User-Agent:GiGA Eyes(compatible;DeviceType/PC;DeviceMode/PC;DeviceId/469F03EC8E35E3371CADF016F93BE670;OSType/PC;OSVersion/1.0;AppVersion/3.4.12;IpAddr/' . env('SERVER_IP') . ')',
            'authToken:' . $authToken
        ];

        $body = [
            'request' => [
                'cam_ids' => ['F00242007441001'],
                'start_time' => '20230612101000',
                'end_time' => '20230612101020'
            ]
        ];

        //yyyymmddhhmmss

        $body = json_encode($body, true);

        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri, $body, $header);
        $returnData = json_decode($returnData['data'], true);

        return $returnData;
    }

    // TEST Account API ==> OK
    public function accountInfo(Request $request)
    {
        $uri = env("GIGA_API_URL") . "/gigaeyes/v1.0/account";
        $authToken = $request->user()['giga_auth_token'];
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic ' . base64_encode(env('GIGA_ID') . ':' . env('GIGA_PASS')),
            'User-Agent:GiGA Eyes(compatible;DeviceType/PC;DeviceMode/PC;DeviceId/469F03EC8E35E3371CADF016F93BE670;OSType/PC;OSVersion/1.0;AppVersion/3.4.12;IpAddr/' . env('SERVER_IP') . ')',
            'authToken:' . $authToken
        ];


        $body = [
            'request' => []
        ];

        $body = json_encode($body, true);

        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri, $body, $header);
        $returnData = json_decode($returnData['data'], true);
        return $returnData;
    }

    // AuthToken 갱신
    public function getAuthToken(Request $request)
    {

        // $uri = "https://openapis.kt.com/gigaeyes/v1.0/authToken";
        $uri = env("GIGA_API_URL") . "/gigaeyes/v1.0/authToken";
        $header = [
            'Content-Type:application/json;charset=UTF-8',
            'Authorization:Basic ' . base64_encode(env('GIGA_ID') . ':' . env('GIGA_PASS'))
        ];


        $body = [
            'request' => [
                'auth_ticket' => $this->generateAuthTicket(),
                'offset_position' => 700,
                'offset_length' => 128,
                'site_id' => env('GIGA_SITEID')
            ]
        ];

        $body = json_encode($body, true);

        $curlController = new CURLController();
        $returnData = $curlController->postCURL($uri, $body, $header);

        $returnData = json_decode($returnData['data'], true);
        if ($returnData['returndescription'] == "Success") {
            $authToken = $returnData['response']['auth_token'];
            User::where('id', $request->user()['id'])->update(['giga_auth_token' => $authToken]);
            return $authToken;
        } else {
            return response()->caps('auth token generate error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // AuthTicket 갱신
    public function generateAuthTicket()
    {
        $result = shell_exec('python ' . app_path() . '/AuthTicket/CertGenerator.py');
        return $result;
    }
}
