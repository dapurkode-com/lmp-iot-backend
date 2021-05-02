<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['barcode', 'name', 'expired_date', 'stock', 'position'];
    protected $cast = [
        'expired_date' => 'date',
        'stock' => 'integer'
    ];
}
