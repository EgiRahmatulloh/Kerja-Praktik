@extends('admin.layouts.app')

@section('title', 'Detail Antrian Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Antrian Surat</h5>
            <div>
                <a href="{{ route('admin.letter-queues.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.letter-queues.edit', $queue->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('admin.filled-letters.show', $queue->filledLetter->id) }}" class="btn btn-primary">
                    <i class="bi bi-file-text"></i> Lihat Surat
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Pemohon</th>
                            <td>{{ $queue->filledLetter->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Surat</th>
                            <td>{{ $queue->filledLetter->letterType->nama_jenis }}</td>
                        </tr>
                        <tr>
                            <th>No. Surat</th>
                            <td>{{ $queue->filledLetter->no_surat }}</td>
                        </tr>
                        <tr>
                            <th>Jadwal</th>
                            <td>{{ $queue->scheduled_date->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($queue->status == 'waiting')
                                <span class="badge bg-warning">Menunggu</span>
                                @elseif($queue->status == 'processing')
                                <span class="badge bg-primary">Diproses</span>
                                @elseif($queue->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @if($queue->notes)
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $queue->notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Tindakan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.letter-queues.update-status', $queue->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Ubah Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="waiting" {{ $queue->status == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="processing" {{ $queue->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                        <option value="completed" {{ $queue->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="notes" class="form-label">Catatan</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $queue->notes }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection