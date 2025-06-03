@extends('admin.layouts.app')

@section('title', 'Manajemen Template Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Template Surat</h5>
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Template
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Template</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $index => $template)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $template->nama_template }}</td>
                            <td>{{ $template->deskripsi }}</td>
                            <td>{{ $template->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.templates.show', $template->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus template ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada template surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection