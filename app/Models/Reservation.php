<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservation';
    protected $primaryKey = 'reservation_no';

    protected $fillable = [
        'reservation_date',
        'reservation_start_time',
        'reservation_end_time',
        'reservation_name',
        'state'
    ];
}
