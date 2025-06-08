@extends('admin.layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Upload Template Surat Baru</h6>
                <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_template" class="form-label">Nama Template</label>
                        <input type="text" class="form-control @error('nama_template') is-invalid @enderror" id="nama_template" name="nama_template" value="{{ old('nama_template') }}" required>
                        @error('nama_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="file_template" class="form-label">File Template (DOCX)</label>
                        <input type="file" class="form-control @error('file_template') is-invalid @enderror" id="file_template" name="file_template" accept=".docx" required>
                        @error('file_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="aktif" name="aktif" value="1" {{ old('aktif') ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif">
                            Aktif
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection