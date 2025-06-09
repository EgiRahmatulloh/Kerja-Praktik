@extends('admin.layouts.app')

@section('title', 'Manajemen Template Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Daftar Template Surat</h6>
                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary mb-3">Upload Template Baru</a>
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nama Template</th>
                                <th scope="col">Status Aktif</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($templates as $template)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $template->nama_template }}</td>
                                    <td>
                                        @if ($template->aktif)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.templates.show', $template->id) }}" class="btn btn-info btn-sm">Detail</a>
                                        <a href="{{ route('admin.templates.edit', $template->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus template ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada template surat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $templates->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
