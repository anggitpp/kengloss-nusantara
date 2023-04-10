<?php

namespace App\Models\Product;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCategory extends Model
{
    use HasFactory, Blameable;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];
}
