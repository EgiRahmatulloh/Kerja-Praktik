@extends('admin.layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Detail Template Surat</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Template:</label>
                    <p>{{ $template->nama_template }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Aktif:</label>
                    <p>
                        @if ($template->aktif)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Template:</label>
                    @if ($template->template_path)
                        <p><a href="{{ $template->public_url }}" target="_blank" class="btn btn-sm btn-primary">Download File</a></p>
                        <small class="form-text text-muted">Nama file: {{ basename($template->template_path) }}</small>
                    @else
                        <p>Tidak ada file template.</p>
                    @endif
                </div>
                <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Kembali</a>
                <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection