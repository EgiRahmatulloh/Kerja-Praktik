@extends('admin.layouts.app')

@section('title', 'Tambah Jenis Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah Jenis Surat</h5>
            <a href="{{ route('admin.letter-types.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.letter-types.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_jenis" class="form-label">Nama Jenis Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_jenis') is-invalid @enderror" id="nama_jenis" name="nama_jenis" value="{{ old('nama_jenis') }}" required>
                            @error('nama_jenis')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>


                </div>

                <div class="row mb-3">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="template_surat_id" class="form-label">Template Surat <span class="text-danger">*</span></label>
                            <select class="form-select @error('template_surat_id') is-invalid @enderror" id="template_surat_id" name="template_surat_id" required>
                                <option value="">Pilih Template</option>
                                @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('template_surat_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->nama_template }}
                                </option>
                                @endforeach
                            </select>
                            @error('template_surat_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1" {{ old('is_public', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_public">Surat Publik</label>
                    </div>
                    <div class="form-text">Jika dicentang, jenis surat ini dapat diakses oleh mahasiswa. Jika tidak, hanya admin yang dapat mengisi surat ini.</div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Pilih Variabel yang Digunakan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($dataItems as $item)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="data_item_{{ $item->id }}" name="data_items[]" value="{{ $item->id }}" {{ (old('data_items') && in_array($item->id, old('data_items'))) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="data_item_{{ $item->id }}">
                                        {{ $item->label }} <small class="text-muted">(<code>{{ $item->key }}</code>)</small>
                                        @if($item->required)
                                        <span class="badge bg-success">Wajib</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('data_items')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.letter-types.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection