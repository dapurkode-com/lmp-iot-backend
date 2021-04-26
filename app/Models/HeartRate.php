<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeartRate extends Model
{
    use HasFactory;

    protected $fillable = ['microtime', 'rate'];
    protected $cast = [
        'rate' => 'integer',
        'microtime' => 'integer'
    ];
}
