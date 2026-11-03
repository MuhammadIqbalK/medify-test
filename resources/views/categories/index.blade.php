@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('categories.status')
            <a href="{{ route('categories.create') }}" class="btn btn-secondary mb-2">+ Kategori Baru</a>
            <div class="card">
                <div class="card-header">Daftar Categories</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('categories.index') }}" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="filter-name" class="form-label">Nama</label>
                            <input id="filter-name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', request('name')) }}" maxlength="255" placeholder="Cari nama kategori">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="filter-code" class="form-label">Kode</label>
                            <input id="filter-code" type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', request('code')) }}" maxlength="255" placeholder="Cari kode kategori">
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead><tr><th>Kode</th><th>Nama</th><th>Items</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse ($categories as $category)
                                <tr>
                                    <td>{{ $category->code }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        @forelse ($category->items as $item)
                                            <div><a href="{{ url('master-items/view/'.$item->kode) }}">{{ $item->nama }}</a></div>
                                        @empty
                                            <span class="text-muted">Belum ada item.</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="{{ route('categories.show', $category) }}">View</a>
                                        <a class="btn btn-info btn-sm" href="{{ route('categories.edit', $category) }}">Edit</a>
                                        @include('categories.delete')
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center">{{ request()->filled('name') || request()->filled('code') ? 'Tidak ada kategori yang sesuai.' : 'Belum ada kategori.' }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
