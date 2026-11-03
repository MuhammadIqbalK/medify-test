@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('categories.status')
            <a href="{{ route('categories.index') }}" class="btn btn-secondary mb-2">Kembali ke Daftar Categories</a>
            <div class="card">
                <div class="card-header">Detail Kategori</div>
                <div class="card-body">
                    <dl>
                        <dt>Kode</dt><dd>{{ $category->code }}</dd>
                        <dt>Nama</dt><dd>{{ $category->name }}</dd>
                    </dl>
                    <a class="btn btn-info" href="{{ route('categories.edit', $category) }}">Edit</a>
                    @include('categories.delete')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
