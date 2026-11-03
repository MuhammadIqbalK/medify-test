<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MasterItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class DownloadsTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_pdf_and_excel_downloads()
    {
        $category = Category::create(['name' => 'Obat', 'code' => 'MED']);
        $other = Category::create(['name' => 'Umum', 'code' => 'GEN']);
        $item = new MasterItem;
        $item->forceFill(['kode' => '001', 'nama' => '=Test item', 'harga_beli' => 20000, 'laba' => 10, 'supplier' => 'Supplier', 'jenis' => 'Obat'])->save();
        $item->categories()->attach([$category->id, $other->id]);
        $pdf = $this->get(route('categories.pdf', $category))->assertOk()->assertDownload('kategori-'.$category->id.'.pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        file_put_contents('/tmp/medify-category-preview.pdf', $pdf->getContent());
        $response = $this->get(route('master-items.excel'))->assertOk()->assertDownload('master-items.xlsx');
        $path = tempnam(sys_get_temp_dir(), 'medify-xlsx');
        try {
            file_put_contents($path, $response->streamedContent());
            $sheet = IOFactory::load($path)->getActiveSheet();
            $this->assertSame('Nama kategori', $sheet->getCell('B1')->getValue());
            $this->assertSame('Obat, Umum', $sheet->getCell('B2')->getValue());
            $this->assertSame('=Test item', $sheet->getCell('C2')->getValue());
            $this->assertSame('s', $sheet->getCell('C2')->getDataType());
            $this->assertEquals(22000, $sheet->getCell('G2')->getCalculatedValue());
        } finally {
            unlink($path);
        }
        $this->get('/categories/999/pdf')->assertNotFound();
    }

    public function test_empty_exports_download_successfully()
    {
        $category = Category::create(['name' => 'Empty', 'code' => 'EMPTY']);
        $this->get(route('categories.pdf', $category))->assertOk();
        $response = $this->get(route('master-items.excel'))->assertOk();
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }
}
