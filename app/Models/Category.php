<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            MasterItem::class,
            'categories_item',
            'id_categories',
            'id_master_item'
        )->withTimestamps();
    }

    public function categoryItems(): HasMany
    {
        return $this->hasMany(CategoriesItem::class, 'id_categories');
    }
}
