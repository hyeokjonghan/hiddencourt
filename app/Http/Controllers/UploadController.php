<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadFileInfo;
use Illuminate\Support\Facades\Request as FileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class UploadController extends Controller
{
    public static $GIGA_VIDEO="video";

    public function fileUpload(Request $request, FileRequest $fileRequest, $uploadDivision) {
        switch ($uploadDivision) {
            case UploadController::$GIGA_VIDEO:
                $uploadFolderPath = "common/video";
                break;
        }

        $target = $request->get('target_no', null);
        $uploadFileInfo = UploadController::uploadMultiFile($fileRequest, $uploadFolderPath, $uploadDivision, $target);
        UploadFileInfo::insert($uploadFileInfo);
        return $uploadFileInfo;
    }

    public function uploadMultiFile(FileRequest $fileRequest, $uploadFolderPath, $uploadName, $target = null) {
        $uploadResult = array();
        if($fileRequest::hasfile($uploadName)) {
            $uploadFile = $fileRequest::file($uploadName);
            if(is_array($uploadFile)) {
                foreach($uploadFile as $i => $file) {
                    $fileSize = $file->getSize();
                    $fileRealName = $file->getClientOriginalName();
                    $fileExtension = $file->getClientOriginalExtension();
                    $fileTempName = uniqid();
                    $filePath = $uploadFolderPath.'/'.$fileTempName.'.'.$fileExtension;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
                    array_push($uploadResult, array(
                        'target_no'=>$target,
                        'upload_type'=>$uploadName,
                        'file_sort'=>$i+1,
                        'file_size'=>$fileSize,
                        'file_real_name'=>$fileRealName,
                        'file_extension'=>$fileExtension,
                        'file_temp_name'=>$fileTempName,
                        'file_path'=>'/'.$filePath,
                        'file_s3_path'=>env('AWS_CLOUDFRONT_S3_URL').'/'.$filePath
                    ));
                }
            } else {
                $fileSize = $fileRequest::file($uploadName)->getSize();                              // 파일 사이즈
                $fileRealName = $fileRequest::file($uploadName)->getClientOriginalName();                  // 원본 파일 명
                $fileExtension = $fileRequest::file($uploadName)->getClientOriginalExtension();            // 확장자
                $fileTempName = uniqid();                                        // 임시 파일 명
                $filePath = $uploadFolderPath.'/'.$fileTempName.'.'.$fileExtension;
                Storage::disk('s3')->put($filePath, file_get_contents($fileRequest::file($uploadName)));
                array_push($uploadResult, array(
                    'target_no' => $target,
                    'upload_type' => $uploadName,
                    'file_size'=>$fileSize,
                    'file_real_name'=>$fileRealName,
                    'file_extension'=>$fileExtension,
                    'file_temp_name'=>$fileTempName,
                    'file_path'=>'/'.$filePath,
                    'file_s3_path'=>env('AWS_CLOUDFRONT_S3_URL').'/'.$filePath
                ));
            }

            return $uploadResult;
        } else {
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        
    }
}
