<?php

namespace App\Models\HiddenCourt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevClip extends Model
{
    use HasFactory;

    protected $connection = "mysql2";
    protected $table = "dev_clip";
    protected $primaryKey = "idx";

    protected $fillable = [
        'cart_idx',
        'phoneid',
        'cart_time',
        'link',
        'regdate',
        'limitdate',
        'is_uploaded',
        'file_path'
    ];
}
