<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'camera';
    protected $primaryKey = 'camera_no';

    protected $fillable = [
        'camera_id',
        'cam_name',
        'mac_id',
        'serial_number',
        'cam_group_id',
        'model_name',
        'cam_firmware',
        'cam_group_name'
    ];
}
