<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Models\HiddenCourt\DevClip;
use Illuminate\Support\Facades\Log;

class ConvertMovie implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */


    private $originPath;
    private $cartInfo;
    private $cartTime;

    public function __construct($filePath, $cartInfo, $cartTime)
    {

        $this->originPath = $filePath;
        $this->cartInfo = $cartInfo;
        $this->cartTime = $cartTime;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO :: 여러 지점을 컨버팅 하는 경우,  동적 컨테이너로 처리해 줘야 함. OS 위에 내부 도커 필요.
        // 도커 컨테이너로 영상 변환을 실행해야 하고, 추가된 예약건에 대해서 영상 변환 속도가 따라가지 못할 수 있기 때문에 동적으로 여러개를 처리해야 할 수 있음.
        // 통합 어플리케이션으로 전환시에 이에 따른 코드 리팩토링이 필요함
        $fileName = uniqid().'.mp4';
        $filePath = "common/video/".$this->cartInfo['phoneid'].'/'.$fileName;
        $ffmpegCommand = 'ffmpeg -i '.env('AWS_CLOUDFRONT_S3_URL').'/'.$this->originPath.' -bsf:a aac_adtstoasc -vcodec copy -c copy -crf 50 '.$fileName;
        Log::info('INIT CHECK FFMPEG CART INFO ==> ');
        Log::info($this->cartInfo);
        shell_exec($ffmpegCommand);

        if(file_exists($fileName)) {
            $fileStorageLog = Storage::disk('s3')->put($filePath, file_get_contents($fileName));
            Log::info('S3 UPLOAD RESULT ==>');
            Log::info($fileStorageLog);
            $clip = new DevClip([
                'cart_idx' => $this->cartInfo['idx'],
                'phoneid' => $this->cartInfo['phoneid'],
                'link' => env('AWS_CLOUDFRONT_S3_URL').'/'.$filePath,
                'cart_time' => $this->cartTime,
                'regdate' => date("Y-m-d H:i:s"),
                'limitdate' => date("Y-m-d", strtotime(date("Y-m-d") . "+7 days"))
            ]);
            $clip->save();
            unlink($fileName);            
        }

    }
}
