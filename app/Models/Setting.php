<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = "setting_param";
    public $incrementing = false;
    protected $fillable = [
        'setting_param',
        'setting_value'
    ];
}
