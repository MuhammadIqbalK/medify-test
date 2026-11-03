<form method="POST" enctype="multipart/form-data">
    @csrf
    @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if($method == 'edit')
    <div class="form-group">
        <label>Kode Barang</label>
        <input type="text" class="form-control" name="kode_barang" required readonly value="{{$item->kode ?? ''}}">
    </div>
    @endif

    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required  value="{{ old('nama', $item->nama) }}">
    </div>

    <div class="form-group">
        <label>Harga Beli</label>
        <input type="number" class="form-control" name="harga_beli" required  value="{{ old('harga_beli', $item->harga_beli) }}">
    </div>

    <div class="form-group">
        <label>Laba (dalam persen)</label>
        <input type="number" class="form-control" name="laba" required  value="{{ old('laba', $item->laba) }}">
    </div>

    @php $selected = old('supplier', $item->supplier); @endphp
    <div class="form-group">
        <label>Supplier</label>
        <select class="form-control" required name="supplier">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            <option @if($selected == 'Tokopaedi') selected @endif>Tokopaedi</option>
            <option @if($selected == 'Bukulapuk') selected @endif>Bukulapuk</option>
            <option @if($selected == 'TokoBagas') selected @endif>TokoBagas</option>
            <option @if($selected == 'E Commurz') selected @endif>E Commurz</option>
            <option @if($selected == 'Blublu') selected @endif>Blublu</option>
        </select>
    </div>

    @php $selected = old('jenis', $item->jenis); @endphp
    <div class="form-group">
        <label>Jenis</label>
        <select class="form-control" required name="jenis">
            <option @if($selected == '') selected @endif value="">--Pilih--</option>
            <option @if($selected == 'Obat') selected @endif>Obat</option>
            <option @if($selected == 'Alkes') selected @endif>Alkes</option>
            <option @if($selected == 'Matkes') selected @endif>Matkes</option>
            <option @if($selected == 'Umum') selected @endif>Umum</option>
            <option @if($selected == 'ATK') selected @endif>ATK</option>
        </select>
    </div>

    <fieldset class="mt-3">
        <legend class="fs-6">Categories</legend>
        @php $selectedCategories = old('categories', $errors->any() ? [] : $item->categories->modelKeys()); @endphp
        @forelse ($categories as $category)
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="categories[]" id="category-{{ $category->id }}" value="{{ $category->id }}" @checked(in_array($category->id, (array) $selectedCategories))>
            <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
        </div>
        @empty
        <p class="text-muted">Belum ada kategori. <a href="{{ route('categories.create') }}">Tambah kategori</a></p>
        @endforelse
    </fieldset>
    <div class="mt-3">
        <label for="image" class="form-label">Image</label>
        @if ($item->image_url)
        <div class="mb-2"><img src="{{ $item->image_url }}" alt="Foto {{ $item->nama }}" class="img-thumbnail" style="max-height: 180px"></div>
        @endif
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="form-control @error('image') is-invalid @enderror" aria-describedby="image-help">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div id="image-help" class="form-text">JPG, PNG, atau WebP, maksimal 2 MB. Kosongkan untuk mempertahankan foto saat ini.</div>
    </div>

    <button class="btn btn-primary mt-3">Submit</button>

</form>