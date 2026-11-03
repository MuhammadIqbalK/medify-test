<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Detail Kategori</title>
<style>
@page { margin: 32px 32px 60px; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #243447; }
h1 { font-size: 20px; margin: 0 0 16px; }
p { margin: 5px 0; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 18px; }
th, td { border: 1px solid #ccd3db; padding: 7px; word-wrap: break-word; }
th { background: #24476a; color: white; text-align: left; }
thead { display: table-header-group; }
tr { page-break-inside: avoid; }
.number { text-align: right; }
footer { position: fixed; bottom: -35px; left: 0; right: 0; font-size: 9px; border-top: 1px solid #ccd3db; padding-top: 8px; }
</style>
</head>
<body>
<footer>Dicetak pada: {{ $printedAt }}</footer>
<h1>Detail Master Kategori</h1>
<p><strong>Nama kategori:</strong> {{ $category->name }}</p>
<p><strong>Kode kategori:</strong> {{ $category->code }}</p>
<table>
<thead><tr><th style="width: 4%">No</th><th style="width: 10%">Kode item</th><th style="width: 24%">Nama item</th><th style="width: 18%">Supplier</th><th>Harga beli (Rp)</th><th style="width: 7%">Laba</th><th>Harga jual (Rp)</th></tr></thead>
<tbody>
@forelse ($category->items as $item)
<tr><td>{{ $loop->iteration }}</td><td>{{ $item->kode }}</td><td>{{ $item->nama }}</td><td>{{ $item->supplier }}</td><td class="number">{{ number_format($item->harga_beli, 2, ',', '.') }}</td><td class="number">{{ $item->laba }}%</td><td class="number">{{ number_format($item->harga_beli * (1 + $item->laba / 100), 2, ',', '.') }}</td></tr>
@empty
<tr><td colspan="7">Belum ada item pada kategori ini.</td></tr>
@endforelse
</tbody>
</table>
</body>
</html>
