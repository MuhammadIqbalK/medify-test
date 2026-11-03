@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary mb-2">Kembali ke Daftar Categories</a>
            <div class="card">
                <div class="card-header">{{ $category->exists ? 'Edit Kategori' : 'Kategori Baru' }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
                        @csrf
                        @if ($category->exists)
                            @method('PUT')
                        @endif
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode</label>
                            <input id="code" type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $category->code) }}" maxlength="255" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" maxlength="255" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
