@extends('admin.layouts.app')

@section('title', 'Manajemen Antrian Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Antrian Surat</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('admin.letter-queues.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cari nama pemohon..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.letter-queues.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Pemohon</th>
                            <th>Jenis Surat</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $index => $queue)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $queue->filledLetter->user->name }}</td>
                            <td>{{ $queue->filledLetter->letterType->nama_jenis }}</td>
                            <td>{{ $queue->scheduled_date->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($queue->status == 'waiting')
                                <span class="badge bg-warning">Menunggu</span>
                                @elseif($queue->status == 'processing')
                                <span class="badge bg-primary">Diproses</span>
                                @elseif($queue->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.letter-queues.show', $queue->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.letter-queues.edit', $queue->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('admin.filled-letters.show', $queue->filledLetter->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-file-text"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada antrian surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $queues->links() }}
            </div>
        </div>
    </div>
</div>
@endsection