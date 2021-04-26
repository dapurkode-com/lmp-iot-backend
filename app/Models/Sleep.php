<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sleep extends Model
{
    use HasFactory;
    protected $fillable = ['start_microtime', 'end_microtime'];
    protected $cast = [
        'end_microtime' => 'integer',
        'start_microtime' => 'integer'
    ];
}
