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

        $fileName = uniqid().'.mp4';
        $filePath = "common/video/".$this->cartInfo['phoneid'].'/'.$fileName;
        $ffmpegCommand = 'ffmpeg -i '.env('AWS_CLOUDFRONT_S3_URL').'/'.$this->originPath.' -bsf:a aac_adtstoasc -vcodec copy -c copy -crf 50 '.$fileName;
        Log::info('INIT CHECK FFMPEG CART INFO ==> ');
        Log::info($this->cartInfo);
        shell_exec($ffmpegCommand);

        if(file_exists($fileName)) {
            Storage::disk('s3')->put($filePath, file_get_contents($fileName));
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
