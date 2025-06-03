@extends('admin.layouts.app')

@section('title', 'Detail Jenis Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Jenis Surat</h5>
            <div>
                <a href="{{ route('admin.letter-types.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.letter-types.edit', $letterType->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nama Jenis Surat</th>
                            <td>{{ $letterType->nama_jenis }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $letterType->deskripsi }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($letterType->is_public)
                                <span class="badge bg-success">Publik</span>
                                <small class="text-muted">(Dapat diakses oleh mahasiswa)</small>
                                @else
                                <span class="badge bg-danger">Privat</span>
                                <small class="text-muted">(Hanya dapat diisi oleh admin)</small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Template</th>
                            <td>
                                <a href="{{ route('admin.templates.show', $letterType->template_surat_id) }}">
                                    {{ $letterType->templateSurat->nama_template }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $letterType->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diperbarui</th>
                            <td>{{ $letterType->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Variabel yang Digunakan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kunci</th>
                                    <th>Label</th>
                                    <th>Tipe Input</th>
                                    <th>Wajib</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($letterType->dataItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $item->key }}</code></td>
                                    <td>{{ $item->label }}</td>
                                    <td>{{ $item->input_type }}</td>
                                    <td>{!! $item->required ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada variabel yang digunakan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Preview Template</h6>
                </div>
                <div class="card-body">
                    <div class="border p-3">
                        {!! $letterType->templateSurat->html_content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection