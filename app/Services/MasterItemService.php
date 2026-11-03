<?php

namespace App\Services;

use App\Models\MasterItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MasterItemService
{
    public function save(array $validated, $method, $id = 0, ?UploadedFile $image = null): void
    {
        if ($method == 'new') {
            $data_item = new MasterItem;
            $kode = MasterItem::withTrashed()->max('id');
            $kode = $kode + 1;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);
        } else {
            $data_item = MasterItem::with('categories')->findOrFail($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $validated['nama'];
        $data_item->harga_beli = $validated['harga_beli'];
        $data_item->laba = $validated['laba'];
        $data_item->kode = $kode;
        $data_item->supplier = $validated['supplier'];
        $data_item->jenis = $validated['jenis'];
        $oldImageUrl = $data_item->image_url;
        $imagePath = null;
        if ($image !== null) {
            $imagePath = $image->store('master-items', 'public');
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
