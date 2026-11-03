<?php

namespace App\Queries;

use App\Models\Category;

class CategoryQuery
{
    public function paginate(array $filters)
    {
        $categories = Category::with('items');

        foreach (['name', 'code'] as $field) {
            if (isset($filters[$field]) && trim($filters[$field]) !== '') {
                $categories->where($field, 'like', '%'.trim($filters[$field]).'%');
            }
        }

        return $categories->orderBy('name')->paginate(5)->withQueryString();
    }
}
