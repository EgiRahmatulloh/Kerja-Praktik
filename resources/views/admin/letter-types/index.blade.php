@extends('admin.layouts.app')

@section('title', 'Manajemen Jenis Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Jenis Surat</h5>
            <a href="{{ route('admin.letter-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Jenis Surat
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jenis Surat</th>
                            <th>Deskripsi</th>
                            <th>Template</th>
                            <th>Jumlah Variabel</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letterTypes as $index => $type)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $type->nama_jenis }}</td>
                            <td>{{ Str::limit($type->deskripsi, 50) }}</td>
                            <td>{{ $type->templateSurat->nama_template }}</td>
                            <td>{{ $type->dataItems->count() }}</td>
                            <td>
                                @if($type->is_public)
                                <span class="badge bg-success">Publik</span>
                                @else
                                <span class="badge bg-danger">Privat</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.letter-types.show', $type->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.letter-types.edit', $type->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.letter-types.destroy', $type->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis surat ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada jenis surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $letterTypes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection