@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('categories.status')
            <a href="{{ route('categories.index') }}" class="btn btn-secondary mb-2">Kembali ke Daftar Master Kategori</a>
            <div class="card">
                <div class="card-header">Detail Kategori</div>
                <div class="card-body">
                    <dl>
                        <dt>Kode</dt><dd>{{ $category->code }}</dd>
                        <dt>Nama</dt><dd>{{ $category->name }}</dd>
                    </dl>
                    <h2 class="h5 mt-4">Daftar Items</h2>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr><th>No</th><th>Kode</th><th>Nama Item</th><th>Supplier</th><th>Harga Beli</th><th>Laba</th><th>Harga Jual</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($category->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->kode }}</td>
                                    <td><a href="{{ url('master-items/view/'.$item->kode) }}">{{ $item->nama }}</a></td>
                                    <td>{{ $item->supplier }}</td>
                                    <td class="text-nowrap">Rp {{ number_format($item->harga_beli, 2, ',', '.') }}</td>
                                    <td>{{ $item->laba }}%</td>
                                    <td class="text-nowrap">Rp {{ number_format($item->harga_beli * (1 + $item->laba / 100), 2, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted">Belum ada item pada kategori ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a class="btn btn-info" href="{{ route('categories.edit', $category) }}">Edit</a>
                    <a class="btn btn-primary" href="{{ route('categories.pdf', $category) }}">Download PDF</a>
                    @include('categories.delete')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
