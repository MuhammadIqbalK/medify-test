<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function downloadExcel()
    {
        $workbook = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $workbook->getActiveSheet();
        $sheet->setTitle('Master Items');
        $sheet->fromArray(['No', 'Nama kategori', 'Nama items', 'Nama supplier', 'Harga', 'Laba', 'Harga jual'], null, 'A1');
        $row = 2;
        foreach (MasterItem::with(['categories' => fn ($query) => $query->orderBy('name')])->orderBy('id')->lazy(500) as $item) {
            $sheet->setCellValue('A'.$row, $row - 1);
            foreach (['B' => $item->categories->pluck('name')->implode(', '), 'C' => $item->nama, 'D' => $item->supplier] as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $sheet->setCellValue('E'.$row, $item->harga_beli);
            $sheet->setCellValue('F'.$row, $item->laba / 100);
            $sheet->setCellValue('G'.$row, '=E'.$row.'*(1+F'.$row.')');
            $row++;
        }
        $lastRow = max(1, $row - 1);
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:G1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF24476A');
        foreach (['A' => 8, 'B' => 35, 'C' => 35, 'D' => 25, 'E' => 20, 'F' => 12, 'G' => 20] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        $sheet->getStyle('A1:G'.$lastRow)->getAlignment()->setWrapText(true)->setVertical('top');
        if ($lastRow > 1) {
            $sheet->getStyle('E2:E'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('F2:F'.$lastRow)->getNumberFormat()->setFormatCode('0.##%');
            $sheet->getStyle('G2:G'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:G'.$lastRow);

        return response()->streamDownload(function () use ($workbook) {
            try {
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($workbook))->save('php://output');
            } finally {
                $workbook->disconnectWorksheets();
            }
        }, 'master-items.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function search(Request $request)
    {
        $request->validate([
            'hargamin' => ['nullable', 'numeric', 'min:0'],
            'hargamax' => array_merge(['nullable', 'numeric', 'min:0'], $request->filled('hargamin') ? ['gte:hargamin'] : []),
        ]);

        $kode = $request->kode;
        $nama = $request->nama;
        $hargamin = $request->hargamin;
        $hargamax = $request->hargamax;

        $data_search = MasterItem::with('categories:id,name');

        if (! empty($kode)) {
            $data_search = $data_search->where('kode', $kode);
        }
        if (! empty($nama)) {
            $data_search = $data_search->where('nama', 'LIKE', '%'.$nama.'%');
        }
        if ($request->filled('hargamin')) {
            $data_search->where('harga_beli', '>=', $hargamin);
        }
        if ($request->filled('hargamax')) {
            $data_search->where('harga_beli', '<=', $hargamax);
        }

        $data_search = $data_search->select('id', 'kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier', 'image_url')->orderBy('id')->get();

        return json_encode([
            'status' => 200,
            'data' => $data_search,
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = new MasterItem;
        } else {
            $item = MasterItem::with('categories')->findOrFail($id);
        }
        $data['item'] = $item;
        $data['method'] = $method;
        $data['categories'] = Category::orderBy('name')->get();

        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::with('categories')->where('kode', $kode)->firstOrFail();

        return view('master_items.single.index', $data);
    }

    public function formSubmit(Request $request, $method, $id = 0)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'laba' => ['required', 'integer', 'min:0'],
            'supplier' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'distinct', 'exists:categories,id'],
        ]);

        if ($method == 'new') {
            $data_item = new MasterItem;
            $kode = MasterItem::withTrashed()->max('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
        } else {
            $data_item = MasterItem::with('categories')->findOrFail($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;
        $oldImageUrl = $data_item->image_url;
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('master-items', 'public');
            if (! $imagePath) {
                throw ValidationException::withMessages(['image' => 'Foto gagal disimpan. Silakan coba lagi.']);
            }
            $data_item->image_url = '/storage/'.$imagePath;
        }

        try {
            DB::transaction(function () use ($data_item, $validated) {
                $data_item->save();
                $data_item->categories()->sync($validated['categories'] ?? []);
            });
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }

        if ($imagePath && $oldImageUrl && preg_match('#^/storage/(master-items/[a-zA-Z0-9]+\.(?:jpg|jpeg|png|webp))$#', $oldImageUrl, $matches)) {
            Storage::disk('public')->delete($matches[1]);
        }

        return redirect('master-items');
    }

    public function delete($id)
    {
        MasterItem::with('categories')->findOrFail($id)->delete();

        return redirect('master-items');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach ($data as $item) {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100, 1000000);
            $item->laba = rand(10, 99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $random = rand(0, 4);

        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];
        $random = rand(0, 4);

        return $array[$random];
    }
}
