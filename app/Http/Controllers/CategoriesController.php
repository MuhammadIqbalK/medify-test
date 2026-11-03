<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
        ]);
        $categories = Category::with('items');

        foreach (['name', 'code'] as $field) {
            if (isset($filters[$field]) && trim($filters[$field]) !== '') {
                $categories->where($field, 'like', '%'.trim($filters[$field]).'%');
            }
        }

        return view('categories.index', [
            'categories' => $categories->orderBy('name')->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('categories.form', ['category' => new Category]);
    }

    public function store(Request $request)
    {
        $category = Category::create($this->validatedData($request));

        return redirect()->route('categories.show', $category)
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validatedData($request));

        return redirect()->route('categories.show', $category)
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        DB::transaction(function () use ($category) {
            $category->categoryItems()->delete();
            $category->delete();
        });

        return redirect()->route('categories.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255'],
        ]);
    }
}
