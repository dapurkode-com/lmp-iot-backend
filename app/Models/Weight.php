<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
    use HasFactory;
    protected $fillable = ['microtime', 'weight'];
    protected $cast = [
        'weight' => 'float',
        'microtime' => 'integer'
    ];
}
