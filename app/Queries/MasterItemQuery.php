<?php

namespace App\Queries;

use App\Models\MasterItem;

class MasterItemQuery
{
    public function search(array $filters, bool $hasMinimum, bool $hasMaximum)
    {
        $kode = ($filters['kode'] ?? null);
        $nama = ($filters['nama'] ?? null);
        $hargamin = ($filters['hargamin'] ?? null);
        $hargamax = ($filters['hargamax'] ?? null);

        $data_search = MasterItem::with('categories:id,name');

        if (! empty($kode)) {
            $data_search = $data_search->where('kode', $kode);
        }
        if (! empty($nama)) {
            $data_search = $data_search->where('nama', 'LIKE', '%'.$nama.'%');
        }
        if ($hasMinimum) {
            $data_search->where('harga_beli', '>=', $hargamin);
        }
        if ($hasMaximum) {
            $data_search->where('harga_beli', '<=', $hargamax);
        }

        $data_search = $data_search->select('id', 'kode', 'nama', 'jenis', 'harga_beli', 'laba', 'supplier', 'image_url')->orderBy('id')->get();

        return $data_search;
    }
}
