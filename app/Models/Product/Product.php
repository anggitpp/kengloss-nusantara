<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'volume',
        'production_date',
        'expired_date',
        'stock',
        'composition',
        'description',
        'photo',
        'qr',
    ];
}
