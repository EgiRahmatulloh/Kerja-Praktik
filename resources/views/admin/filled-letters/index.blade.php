@extends('admin.layouts.app')

@section('title', 'Manajemen Pengajuan Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan Surat</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('admin.filled-letters.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="dicetak" {{ request('status') == 'dicetak' ? 'selected' : '' }}>Dicetak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="letter_type_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Jenis Surat</option>
                            @foreach($letterTypes as $type)
                            <option value="{{ $type->id }}" {{ request('letter_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->nama_jenis }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cari nama pemohon..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>No. Surat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($letters as $index => $letter)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $letter->user->name }}</td>
                            <td>{{ $letter->letterType->nama_jenis }}</td>
                            <td>{{ $letter->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($letter->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @elseif($letter->status == 'approved')
                                <span class="badge bg-success">Disetujui</span>
                                @elseif($letter->status == 'dicetak')
                                <span class="badge bg-primary">Dicetak</span>
                                @endif
                            </td>
                            <td>{{ $letter->no_surat ?: '-' }}</td>
                            <td>
                            <td>
                                <a href="{{ route('admin.filled-letters.show', $letter->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if($letter->status == 'pending')
                                <a href="{{ route('admin.filled-letters.edit', $letter->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                @if($letter->status == 'approved')
                                <a href="{{ route('admin.filled-letters.edit', $letter->id) }}" class="btn btn-sm btn-warning" title="Edit Status">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @endif

                                @if($letter->status == 'approved' || $letter->status == 'dicetak')
                                <a href="{{ route('admin.filled-letters.pdf', $letter->id) }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada pengajuan surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $letters->links() }}
            </div>
        </div>
    </div>
</div>
@endsection