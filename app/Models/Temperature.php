<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temperature extends Model
{
    use HasFactory;

    protected $fillable = ['microtime', 'temperature'];
    protected $cast = [
        'temperature' => 'float',
        'microtime' => 'integer'
    ];
}
