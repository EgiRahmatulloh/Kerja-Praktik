@extends('admin.layouts.app')

@section('title', 'Edit Pengajuan Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Pengajuan Surat</h5>
            <a href="{{ route('admin.filled-letters.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Pemohon</th>
                            <td>{{ $letter->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Surat</th>
                            <td>{{ $letter->letterType->nama_jenis }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>{{ $letter->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <form action="{{ route('admin.filled-letters.update', $letter->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Data Surat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(is_array($letter->filled_data))
                            @foreach($letter->filled_data as $key => $value)
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label class="form-label" for="data_{{ $key }}">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                    <input type="text" class="form-control" id="data_{{ $key }}" name="data[{{ $key }}]" value="{{ $value }}">
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    Data surat tidak valid. Silahkan hubungi administrator.
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Status dan Informasi Surat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ $letter->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $letter->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="rejected" {{ $letter->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                        <option value="dicetak" {{ $letter->status == 'dicetak' ? 'selected' : '' }}>Dicetak</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_surat" class="form-label">No. Surat</label>
                                    <input type="text" class="form-control" id="no_surat" name="no_surat" value="{{ $letter->no_surat }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_surat" class="form-label">Kode Surat</label>
                                    <input type="text" class="form-control" id="kode_surat" value="{{ $letter->kode_surat }}" readonly>
                                    <div class="form-text">Kode surat diambil otomatis dari template surat</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="catatan_admin" class="form-label">Catatan Admin</label>
                                    <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3">{{ $letter->catatan_admin }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.filled-letters.show', $letter->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validasi form sebelum submit
        $('form').submit(function(e) {
            const status = $('#status').val();
            const catatan = $('#catatan_admin').val();

            // Jika status ditolak, catatan admin wajib diisi
            if (status === 'rejected' && !catatan.trim()) {
                e.preventDefault();
                alert('Catatan admin wajib diisi jika status ditolak!');
                return false;
            }

            // Konfirmasi perubahan status
            if (status !== '{{ $letter->status }}') {
                let message = "Apakah Anda yakin ingin mengubah status surat?";

                if (status === 'approved') {
                    message = "Apakah Anda yakin akan menyetujui surat ini?";
                } else if (status === 'rejected') {
                    message = "Apakah Anda yakin akan menolak surat ini?";
                } else if (status === 'pending') {
                    message = "Apakah Anda yakin akan mengubah status menjadi pending?";
                } else if (status === 'dicetak') {
                    message = "Apakah Anda yakin akan mengubah status menjadi dicetak?";
                }

                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
</script>
@endpush