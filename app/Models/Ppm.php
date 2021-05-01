<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppm extends Model
{
    use HasFactory;

    protected $fillable = ['microtime', 'ppm'];
    protected $cast = [
        'ppm' => 'integer',
        'microtime' => 'integer'
    ];
}
