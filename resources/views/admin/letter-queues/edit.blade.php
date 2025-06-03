@extends('admin.layouts.app')

@section('title', 'Edit Antrian Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Antrian Surat</h5>
            <a href="{{ route('admin.letter-queues.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.letter-queues.update', $queue->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="scheduled_date" class="form-label">Jadwal</label>
                            <input type="datetime-local" class="form-control" id="scheduled_date" name="scheduled_date" value="{{ $queue->scheduled_date->format('Y-m-d\\TH:i') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="waiting" {{ $queue->status == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                                <option value="processing" {{ $queue->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                <option value="completed" {{ $queue->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
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
@endsection