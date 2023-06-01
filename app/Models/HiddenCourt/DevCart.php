<?php

namespace App\Models\HiddenCourt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevCart extends Model
{
    use HasFactory;

    protected $connection = "mysql2";
    protected $table = "dev_cart";

    protected $primaryKey = "idx";
}
