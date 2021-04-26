<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalorieExpended extends Model
{
    use HasFactory;

    protected $fillable = ['microtime', 'calorie'];
    protected $cast = [
        'calorie' => 'float',
        'microtime' => 'integer'
    ];
}
