@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal Pelayanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Tambah Jadwal Pelayanan</h4>
                    <a href="{{ route('admin.service-schedules.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.service-schedules.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Jam Mulai Pelayanan</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', '08:00') }}" required>
                        @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">Jam Selesai Pelayanan</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', '16:00') }}" required>
                        @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="processing_time" class="form-label">Lama Proses (menit)</label>
                        <input type="number" class="form-control @error('processing_time') is-invalid @enderror" id="processing_time" name="processing_time" value="{{ old('processing_time', 10) }}" required min="1">
                        <small class="form-text text-muted">Waktu rata-rata yang dibutuhkan untuk memproses satu surat</small>
                        @error('processing_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection