<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): void
    {
        $category->update($data);
    }

    public function delete(Category $category): void
    {
        DB::transaction(function () use ($category) {
            $category->categoryItems()->delete();
            $category->delete();
        });
    }
}
