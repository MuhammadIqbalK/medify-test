<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoriesItem extends Model
{
    use HasFactory;

    protected $table = 'categories_item';

    protected $fillable = [
        'id_categories',
        'id_master_item',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_categories');
    }

    public function masterItem(): BelongsTo
    {
        return $this->belongsTo(MasterItem::class, 'id_master_item');
    }
}
