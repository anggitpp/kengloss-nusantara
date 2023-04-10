<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'category_id',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(MasterCategory::class, 'category_id');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(MasterFile::class, 'file_id');
    }
}
