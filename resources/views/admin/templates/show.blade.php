@extends('admin.layouts.app')

@section('title', 'Detail Template Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Template Surat</h5>
            <div>
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
                <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama Template</th>
                            <td>{{ $template->nama_template }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $template->deskripsi }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $template->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>{{ $template->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Kode HTML Template</h6>
                </div>
                <div class="card-body">
                    <div class="border p-3 bg-light">
                        <pre><code>{{ $template->html_content }}</code></pre>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Preview Template</h6>
                </div>
                <div class="card-body">
                    <div class="border p-3">
                        {!! $template->html_content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection