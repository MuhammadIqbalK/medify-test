<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'categories_item',
            'id_master_item',
            'id_categories'
        )->withTimestamps();
    }

    public function categoryItems(): HasMany
    {
        return $this->hasMany(CategoriesItem::class, 'id_master_item');
    }
}
