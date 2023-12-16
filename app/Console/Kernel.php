<?php

namespace App\Console;

use App\Http\Controllers\HiddenCourt\ClipController;
use App\Models\HiddenCourt\DevClip;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function() {
            Log::info('== RUNNING SCHEDULE SET CLIP ==');
            $clipController = new ClipController();
            $clipController->setClipToday();
        })->hourlyAt(10);

        $schedule->call(function() {
            $checkUploadedFileList = DevClip::where('is_uploaded', false)->get();
            
            foreach($checkUploadedFileList as $clip) {
                // s3 file exists가 도메인 포함인지 확인 해야 함 도메인 빼고 라고 함
                if(Storage::disk('s3')->exists($clip->file_path)) {
                    DevClip::where('idx', $clip->idx)
                    ->update([
                        'is_uploaded'=>true
                    ]);
                    $fileName = explode('/', $clip->file_path);
                    $fileName = $fileName[count($fileName)-1];
                    unlink($fileName);
                }
            }
            
        })->hourlyAt(50);
        // ->everyMinute();
        // $schedule->command('queue:work --daemon --queue=high,default --timeout=1000000')->everyMinute()->withoutOverlapping();

        // screen -S laravel_queue
        // queue:work --daemon --queue=high,default --timeout=1000000
        // Crtl+a after d
        // screen -r laravel_queue

        // 다음에 정상작동 확인 안되면 supervisor로 추가 구성 후 처리
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
