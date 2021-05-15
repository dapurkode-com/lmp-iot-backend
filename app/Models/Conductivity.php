<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductivity extends Model
{
    use HasFactory;

    protected $fillable = ['microtime', 'conductivity'];
    protected $cast = [
        'conductivity' => 'float',
        'microtime' => 'integer'
    ];
}
