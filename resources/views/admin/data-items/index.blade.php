@extends('admin.layouts.app')

@section('title', 'Manajemen Variabel Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Variabel Surat</h5>
            <a href="{{ route('admin.data-items.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Variabel
            </a>
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $item->key }}</code></td>
                            <td>{{ $item->label }}</td>
                            <td>{{ $item->tipe_input }}</td>
                            <td>{!! $item->required ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.data-items.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.data-items.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus variabel ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada variabel surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $dataItems->links() }}
            </div>
        </div>
    </div>
</div>
@endsection