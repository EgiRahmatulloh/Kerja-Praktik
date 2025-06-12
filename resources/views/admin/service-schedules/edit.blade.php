@extends('admin.layouts.app')

@section('title', 'Edit Jadwal Pelayanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Edit Jadwal Pelayanan</h4>
                    <a href="{{ route('admin.service-schedules.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.service-schedules.update', $schedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Jam Mulai Pelayanan</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}" required>
                        @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">Jam Selesai Pelayanan</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}" required>
                        @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="break_start_time" class="form-label">Jam Mulai Istirahat (Opsional)</label>
                        <input type="time" class="form-control @error('break_start_time') is-invalid @enderror" id="break_start_time" name="break_start_time" value="{{ old('break_start_time', $schedule->break_start_time ? \Carbon\Carbon::parse($schedule->break_start_time)->format('H:i') : '') }}">
                        <small class="form-text text-muted">Jam mulai istirahat, tidak ada antrian surat pada jam ini</small>
                        @error('break_start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="break_end_time" class="form-label">Jam Selesai Istirahat (Opsional)</label>
                        <input type="time" class="form-control @error('break_end_time') is-invalid @enderror" id="break_end_time" name="break_end_time" value="{{ old('break_end_time', $schedule->break_end_time ? \Carbon\Carbon::parse($schedule->break_end_time)->format('H:i') : '') }}">
                        <small class="form-text text-muted">Jam selesai istirahat</small>
                        @error('break_end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="processing_time" class="form-label">Lama Proses (menit)</label>
                        <input type="number" class="form-control @error('processing_time') is-invalid @enderror" id="processing_time" name="processing_time" value="{{ old('processing_time', $schedule->processing_time) }}" required min="1">
                        <small class="form-text text-muted">Waktu rata-rata yang dibutuhkan untuk memproses satu surat</small>
                        @error('processing_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validasi jam istirahat
    $('#break_start_time, #break_end_time').on('change', function() {
        var breakStart = $('#break_start_time').val();
        var breakEnd = $('#break_end_time').val();
        var serviceStart = $('#start_time').val();
        var serviceEnd = $('#end_time').val();
        
        if (breakStart && breakEnd) {
            if (breakStart >= breakEnd) {
                alert('Jam selesai istirahat harus lebih besar dari jam mulai istirahat!');
                $('#break_end_time').val('');
                return;
            }
        }
        
        if (breakStart && serviceStart && breakStart <= serviceStart) {
            alert('Jam mulai istirahat harus setelah jam mulai pelayanan!');
            $('#break_start_time').val('');
            return;
        }
        
        if (breakEnd && serviceEnd && breakEnd >= serviceEnd) {
            alert('Jam selesai istirahat harus sebelum jam selesai pelayanan!');
            $('#break_end_time').val('');
            return;
        }
    });
    
    // Validasi jam pelayanan
    $('#start_time, #end_time').on('change', function() {
        var breakStart = $('#break_start_time').val();
        var breakEnd = $('#break_end_time').val();
        var serviceStart = $('#start_time').val();
        var serviceEnd = $('#end_time').val();
        
        if (breakStart && serviceStart && breakStart <= serviceStart) {
            alert('Jam mulai istirahat harus setelah jam mulai pelayanan!');
            $('#break_start_time').val('');
        }
        
        if (breakEnd && serviceEnd && breakEnd >= serviceEnd) {
            alert('Jam selesai istirahat harus sebelum jam selesai pelayanan!');
            $('#break_end_time').val('');
        }
    });
});
</script>
@endpush