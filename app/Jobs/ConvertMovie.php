<?php

namespace App\Jobs;

use App\Models\HiddenCourt\DevCart;
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
        $ffmpegResult = shell_exec($ffmpegCommand);
        Log::info($ffmpegCommand);
        // shell_exec 의 결과값을 받아와야 함

        if($ffmpegResult) {
            // 로직 자체를 ffmpeg가 반환 성공 이후에 처리되도록 함
            $fileStorageLog = Storage::disk('s3')->put($filePath, file_get_contents($fileName));
            Log::info('S3 UPLOAD RESULT ==>');
            Log::info($fileStorageLog);
            // 만약에 $fileStorageLog 가 false면 해당 값으로 큐를 재등록 하는 형태로 진행 해야 할 듯
            $clip = new DevClip([
                'cart_idx' => $this->cartInfo['idx'],
                'phoneid' => $this->cartInfo['phoneid'],
                'link' => env('AWS_CLOUDFRONT_S3_URL').'/'.$filePath,
                'file_path'=>$filePath,
                'cart_time' => $this->cartTime,
                'regdate' => date("Y-m-d H:i:s"),
                'limitdate' => date("Y-m-d", strtotime(date("Y-m-d") . "+7 days"))
            ]);
            $clip->save();

            // TODO :: 원본 영상 업로드 된 것 S3 상에서 확인하고, 영상 정보 삭제 하는 스케줄 만들고 아래 내용 처리 해줘야 함
        } else {
            // 영상 변환 실패시, 큐에 등록된 것 취소 (재등록 시켜야 함)
            DevCart::where('idx', $this->cartInfo['idx'])
            ->update([
                'is_convert_ready'=>true
            ]);
            Log::info('영상 변환 실패');
            unlink($fileName);
        }

    }
}
