<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservation_video';
    protected $primaryKey = 'reservation_video_no';

    protected $fillable = [
        'reservation_no',
        'camera_no',
        'vedio_url',
        'vedio_storage_url',
        'state'
    ];
}
