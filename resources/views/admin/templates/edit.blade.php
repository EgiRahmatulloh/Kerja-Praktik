@extends('admin.layouts.app')

@section('title', 'Edit Template Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Template Surat</h5>
            @if($template->kategori_surat === 'form')
                <a href="{{ route('admin.surat-form.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @elseif($template->kategori_surat === 'non_form')
                <a href="{{ route('admin.surat-non-form.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @else
                <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @endif
        </div>
        <div class="card-body">
            @if($template->kategori_surat === 'form')
                <form action="{{ route('admin.surat-form.update', $template->id) }}" method="POST">
            @elseif($template->kategori_surat === 'non_form')
                <form action="{{ route('admin.surat-non-form.update', $template->id) }}" method="POST">
            @else
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
            @endif
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_template" class="form-label">Nama Template <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_template') is-invalid @enderror" id="nama_template" name="nama_template" value="{{ old('nama_template', $template->nama_template) }}" required>
                            @error('nama_template')
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
                            <label for="kategori_surat" class="form-label">Kategori Surat <span class="text-danger">*</span></label>
                            <select class="form-control @error('kategori_surat') is-invalid @enderror" id="kategori_surat" name="kategori_surat" required>
                                <option value="default" {{ old('kategori_surat', $template->kategori_surat ?? 'default') === 'default' ? 'selected' : '' }}>Template Default</option>
                                <option value="form" {{ old('kategori_surat', $template->kategori_surat) === 'form' ? 'selected' : '' }}>Surat dengan Form</option>
                                <option value="non_form" {{ old('kategori_surat', $template->kategori_surat) === 'non_form' ? 'selected' : '' }}>Surat tanpa Form</option>
                            </select>
                            <div class="form-text">Pilih kategori untuk menentukan menu penyimpanan surat</div>
                            @error('kategori_surat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="action_type" class="form-label">Aksi Penyimpanan <span class="text-danger">*</span></label>
                            <select class="form-control @error('action_type') is-invalid @enderror" id="action_type" name="action_type" required>
                                <option value="update_original" {{ old('action_type') === 'update_original' ? 'selected' : '' }}>Update Template Asli</option>
                                <option value="save_as_new" {{ old('action_type') === 'save_as_new' ? 'selected' : '' }}>Simpan sebagai Template Baru</option>
                            </select>
                            <div class="form-text">Pilih untuk update template asli atau buat template baru</div>
                            @error('action_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="html_content" class="form-label">Kode HTML Template <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('html_content') is-invalid @enderror" id="html_content" name="konten_template" rows="15" required>{{ old('html_content', $template->html_content) }}</textarea>
                    @error('html_content')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="form-text">
                        Gunakan variabel dalam format <code>@{{ $nama }}</code> untuk data yang akan diisi oleh pengguna (contoh: @{{ $nama }}, @{{ $nik }}, @{{ $alamat }}, dll).
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    @if($template->kategori_surat === 'form')
                        <a href="{{ route('admin.surat-form.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Batal
                        </a>
                    @elseif($template->kategori_surat === 'non_form')
                        <a href="{{ route('admin.surat-non-form.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Batal
                        </a>
                    @else
                        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x"></i> Batal
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Bootstrap 4 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#html_content').summernote({
            placeholder: 'Masukkan konten template surat...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endsection