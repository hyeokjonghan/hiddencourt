<?php

namespace App\Console\Commands;

use App\Http\Controllers\HiddenCourt\CartController;
use App\Http\Controllers\HiddenCourt\ClipController;
use App\Http\Controllers\ktApiController;
use App\Http\Controllers\Lib\CURLController;
use App\Models\Camera;
use Illuminate\Console\Command;

class ConvertMovie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:convert-movie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Movie Test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $cartController = new CartController();
        $todayCartList = $cartController->getTodayReservation();
        $this->info('today Reservation : '. $todayCartList);
        $ktApiController = new ktApiController();
        $authToken = $ktApiController->getAuthToken();
        if($authToken) {
            foreach($todayCartList as $todayCart) {
                $this->info('today Cart : '. $todayCart);
                $coatName = str_replace(' ', '', $todayCart['coatname']);
                $cameraInfo = Camera::select('*')->where('cam_name', $coatName)->first();
                $this->info('Camera Info : '. $cameraInfo);
                $time = $todayCart['first_time'];
                $startTimeStamp = strtotime($todayCart['od_regdate'] . " " . $time . ":00" . "+5 minutes");
                $startTime = date("YmdHis", $startTimeStamp);
                $endTimeStamp = strtotime($todayCart['od_regdate'] . " " . $time . ":00" . "+30 minutes");
                $endTime = date("YmdHis", $endTimeStamp);
                $this->info($startTime);
                $this->info($endTime);
                $uri = env("GIGA_API_URL")."/gigaeyes/v1.0/recordVideo";
                $header = [
                    'Content-Type:application/json;charset=UTF-8',
                    'Authorization:Basic '.base64_encode(env('GIGA_ID').':'.env('GIGA_PASS')),
                    'User-Agent:GiGA Eyes(compatible;DeviceType/PC;DeviceMode/PC;DeviceId/469F03EC8E35E3371CADF016F93BE670;OSType/PC;OSVersion/1.0;AppVersion/3.4.12;IpAddr/' . env('SERVER_IP') . ')',
                    'authToken:'.$authToken
                ];
        
                $body = [
                    'request'=>[
                        'cam_id'=>$cameraInfo['camera_id'],
                        'start_time'=>$startTime,
                        'end_time'=>$endTime
                    ]
                ];
        
                //yyyymmddhhmmss
        
                $body = json_encode($body, true);
                
                $curlController = new CURLController();
                $returnData = $curlController->postCURL($uri, $body, $header);
                $this->info('Get Video Info => ');
                $this->info(json_encode($returnData, true));
                // $videoInfo = $ktApiController->recordVideo($authToken, $cameraInfo['camera_id'], $startTime, $endTime);
                // $this->info('Get Video Info :', $videoInfo);
            }
        }
    }
}
