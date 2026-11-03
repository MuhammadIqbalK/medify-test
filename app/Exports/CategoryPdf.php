<?php

namespace App\Exports;

use App\Models\Category;

class CategoryPdf
{
    public function download(Category $category)
    {
        $category->load(['items' => fn ($query) => $query->orderBy('nama')]);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('categories.pdf', [
            'category' => $category,
            'printedAt' => now('Asia/Jakarta')->format('d/m/Y H:i:s').' WIB',
        ])->setPaper('a4', 'landscape')->download('kategori-'.$category->id.'.pdf');
    }
}
