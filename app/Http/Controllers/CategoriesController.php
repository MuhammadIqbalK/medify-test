<?php

namespace App\Http\Controllers;

use App\Exports\CategoryPdf;
use App\Http\Requests\FilterCategoriesRequest;
use App\Http\Requests\SaveCategoryRequest;
use App\Models\Category;
use App\Queries\CategoryQuery;
use App\Services\CategoryService;

class CategoriesController extends Controller
{
    public function __construct(private CategoryService $categories)
    {
    }

    public function index(FilterCategoriesRequest $request, CategoryQuery $query)
    {
        return view('categories.index', ['categories' => $query->paginate($request->validated())]);
    }

    public function downloadPdf(Category $category, CategoryPdf $export)
    {
        return $export->download($category);
    }

    public function create()
    {
        return view('categories.form', ['category' => new Category]);
    }

    public function store(SaveCategoryRequest $request)
    {
        $category = $this->categories->create($request->validated());

        return redirect()->route('categories.show', $category)
            ->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        $category->load(['items' => fn ($query) => $query->orderBy('nama')]);

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(SaveCategoryRequest $request, Category $category)
    {
        $this->categories->update($category, $request->validated());

        return redirect()->route('categories.show', $category)
            ->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $this->categories->delete($category);

        return redirect()->route('categories.index')
            ->with('status', 'Kategori berhasil dihapus.');
    }
}
