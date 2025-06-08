@extends('admin.layouts.app')

@section('title', 'Detail Pengajuan Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Pengajuan Surat</h5>
            <div>
                <a href="{{ route('admin.filled-letters.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                @if($letter->status == 'pending')
                <a href="{{ route('admin.filled-letters.edit', $letter->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                @endif

                @if($letter->status == 'approved' || $letter->status == 'printed')
                <a href="{{ route('admin.filled-letters.print', $letter->id) }}" class="btn btn-success" target="_blank">
                    <i class="bi bi-printer"></i> Cetak
                </a>
                <a href="{{ route('admin.filled-letters.docx', $letter->id) }}" class="btn btn-primary ms-2">
                    <i class="bi bi-download"></i> Download DOCX
                </a>
                @endif
            </div>
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
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($letter->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @elseif($letter->status == 'approved')
                                <span class="badge bg-success">Disetujui</span>
                                @elseif($letter->status == 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                                @elseif($letter->status == 'printed')
                                <span class="badge bg-primary">Dicetak</span>
                                @endif
                            </td>
                        </tr>
                        @if($letter->status != 'pending')
                        <tr>
                            <th>No. Surat</th>
                            <td>{{ $letter->no_surat ?: '-' }}</td>
                        </tr>

                        @endif
                        @if($letter->catatan_admin)
                        <tr>
                            <th>Catatan Admin</th>
                            <td>{{ $letter->catatan_admin }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Data Surat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($letter->filled_data as $key => $value)
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                <input type="text" class="form-control" value="{{ $value }}" readonly>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($letter->status == 'pending' || $letter->status == 'approved' || $letter->status == 'rejected')
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">Tindakan</h6>
                </div>
                <div class="card-body">
                    <form id="statusForm" action="{{ route('admin.filled-letters.update-status', $letter->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Ubah Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ $letter->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $letter->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="rejected" {{ $letter->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                        <option value="printed" {{ $letter->status == 'printed' ? 'selected' : '' }}>Dicetak</option>
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
                                    <label for="catatan_admin" class="form-label">Catatan Admin / Alasan Penolakan</label>
                                    <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3">{{ $letter->catatan_admin }}</textarea>
                                    <div class="form-text">Wajib diisi jika status surat ditolak</div>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="submitBtn" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Preview Surat</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="toggleEditBtn">
                        <i class="bi bi-pencil"></i> Edit Template
                    </button>
                </div>
                <div class="card-body">
                    <!-- Mode Preview -->
                    <div id="previewMode" class="border p-3">
                        {!! $preview !!}
                    </div>

                    <!-- Mode Edit -->
                    <div id="editMode" class="border p-3" style="display: none;">
                        <form id="templateEditForm" action="{{ route('admin.filled-letters.update-template', $letter->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <textarea id="templateEditor" name="template_content" class="form-control" rows="15" style="font-family: monospace;">{{ $content }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" id="cancelEditBtn">Batal</button>
                                <button type="button" class="btn btn-primary" id="previewEditBtn">Preview</button>
                                <button type="submit" class="btn btn-success ms-2">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>

                    <!-- Mode Preview Hasil Edit -->
                    <div id="previewEditMode" class="border p-3 mt-3" style="display: none;">
                        <h6 class="mb-3">Preview Hasil Edit:</h6>
                        <div id="previewEditContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Perubahan Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                Apakah Anda yakin ingin mengubah status surat?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Saya Yakin</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Peringatan -->
<div class="modal fade" id="warningModal" tabindex="-1" aria-labelledby="warningModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warningModalTitle">Peringatan!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="warningModalBody">
                Pesan peringatan akan ditampilkan di sini.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Ketika tombol submit ditekan
        $('#submitBtn').click(function(e) {
            e.preventDefault(); // Mencegah form submit langsung

            const status = $('#status').val();
            const catatan = $('#catatan_admin').val();
            let modalMessage = "Apakah Anda yakin ingin mengubah status surat?";

            // Periksa status yang dipilih
            if (status === 'approved') {
                modalMessage = "Apakah Anda yakin akan menyetujui surat ini?";
            } else if (status === 'rejected') {
                // Periksa apakah catatan admin diisi
                if (!catatan.trim()) {
                    // Tampilkan modal peringatan untuk catatan kosong
                    $('#warningModalTitle').text('Peringatan!');
                    $('#warningModalBody').text('Catatan admin wajib diisi jika status ditolak!');
                    $('#warningModal').modal('show');
                    // Fokus pada field catatan admin setelah modal ditutup
                    $('#warningModal').on('hidden.bs.modal', function() {
                        $('#catatan_admin').focus();
                    });
                    return false;
                }
                modalMessage = "Apakah Anda yakin akan menolak surat ini?";
            } else if (status === 'pending') {
                modalMessage = "Apakah Anda yakin akan mengubah status menjadi pending?";
            } else if (status === 'printed') {
                modalMessage = "Apakah Anda yakin akan mengubah status menjadi dicetak?";
            }

            // Tampilkan modal konfirmasi
            $('#confirmModalBody').text(modalMessage);
            $('#confirmModal').modal('show');
        });

        // Ketika tombol konfirmasi di modal ditekan
        $('#confirmSubmit').click(function() {
            // Submit form
            document.getElementById('statusForm').submit();
        });

        // Fungsi untuk edit template surat
        $('#toggleEditBtn').click(function() {
            $('#previewMode').hide();
            $('#editMode').show();
            $('#previewEditMode').hide();
        });

        $('#cancelEditBtn').click(function() {
            $('#editMode').hide();
            $('#previewEditMode').hide();
            $('#previewMode').show();
        });

        $('#previewEditBtn').click(function() {
            // Ambil konten template yang diedit
            const editedTemplate = $('#templateEditor').val();

            // Tampilkan preview hasil edit
            $('#previewEditContent').html(processTemplate(editedTemplate));
            $('#previewEditMode').show();
        });

        // Fungsi untuk memproses template dengan data yang diisi
        function processTemplate(template) {
            // Dapatkan data yang diisi dari halaman
            const filledData = {};
            @foreach($letter->filled_data as $key => $value)
            filledData['{{ $key }}'] = '{{ $value }}';
            @endforeach

            // Ganti variabel dengan data yang diisi
            let processedContent = template;
            for (const [key, value] of Object.entries(filledData)) {
                // Ganti semua format variabel yang mungkin
                processedContent = processedContent.replace(new RegExp('\{\{ \$' + key + ' \}\}', 'g'), value);
                processedContent = processedContent.replace(new RegExp('\{\{\$' + key + '\}\}', 'g'), value);
                processedContent = processedContent.replace(new RegExp('\{\{ \$data->' + key + ' \}\}', 'g'), value);
                processedContent = processedContent.replace(new RegExp('\{\{\$data->' + key + '\}\}', 'g'), value);
            }

            processedContent = processedContent.replace(/\{\{ \$noSurat \}\}/g, '{{ $letter->no_surat }}');
            processedContent = processedContent.replace(/\{\{\$noSurat\}\}/g, '{{ $letter->no_surat }}');
            processedContent = processedContent.replace(/\{\{ \$data->noSurat \}\}/g, '{{ $letter->no_surat }}');
            processedContent = processedContent.replace(/\{\{\$data->noSurat\}\}/g, '{{ $letter->no_surat }}');


            return processedContent;
        }
    });
</script>
@endpush