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
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="printed" {{ request('status') == 'printed' ? 'selected' : '' }}>Dicetak</option>
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
                            <input type="text" class="form-control" name="search" placeholder="Cari nama pemohon..." value="{{ $searchQuery }}">
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
                        <tr class="letter-row" data-letter-id="{{ $letter->id }}" style="cursor: pointer;">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $letter->user->name }}</td>
                            <td>{{ $letter->letterType->nama_jenis }}</td>
                            <td>{{ $letter->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($letter->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($letter->status == 'approved')
                                <span class="badge bg-success">Disetujui</span>
                                @elseif($letter->status == 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                                @elseif($letter->status == 'printed')
                                <span class="badge bg-primary">Dicetak</span>
                                @endif
                            </td>
                            <td>{{ $letter->no_surat ?: '-' }}</td>
                            <td>
                                @if($letter->status == 'approved' || $letter->status == 'printed')
                                <a href="{{ route('admin.filled-letters.docx', $letter->id) }}" class="btn btn-sm btn-primary" title="Download DOCX" onclick="event.stopPropagation();">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        <tr class="letter-details" id="details-{{ $letter->id }}" style="display: none;">
                            <td colspan="7">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">Informasi Pemohon</h6>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th width="40%">Pemohon:</th>
                                                        <td>{{ $letter->user->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jenis Surat:</th>
                                                        <td>{{ $letter->letterType->nama_jenis }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Pengajuan:</th>
                                                        <td>{{ $letter->created_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">Status & Informasi Surat</h6>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th width="40%">Status:</th>
                                                        <td>
                                                            <select class="form-select form-select-sm status-select" data-letter-id="{{ $letter->id }}" data-current-status="{{ $letter->status }}" style="width: auto; display: inline-block;">
                                                                <option value="pending" {{ $letter->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="approved" {{ $letter->status == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                                                <option value="rejected" {{ $letter->status == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                                                <option value="printed" {{ $letter->status == 'printed' ? 'selected' : '' }}>Dicetak</option>
                                                            </select>
                                                            <span class="status-badge ms-2">
                                                                @if($letter->status == 'pending')
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                                @elseif($letter->status == 'approved')
                                                                <span class="badge bg-success">Disetujui</span>
                                                                @elseif($letter->status == 'rejected')
                                                                <span class="badge bg-danger">Ditolak</span>
                                                                @elseif($letter->status == 'printed')
                                                                <span class="badge bg-primary">Dicetak</span>
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>No. Surat:</th>
                                                        <td>{{ $letter->no_surat ?: '-' }}</td>
                                                    </tr>
                                                    @if($letter->catatan_admin)
                                                    <tr>
                                                        <th>Catatan Admin:</th>
                                                        <td>{{ $letter->catatan_admin }}</td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>

                                        @if(is_array($letter->filled_data) && count($letter->filled_data) > 0)
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6 class="text-primary mb-3">Data Surat</h6>
                                                <div class="row">
                                                    @foreach($letter->filled_data as $key => $value)
                                                    <div class="col-md-6 mb-2">
                                                        <p class="mb-0 text-primary"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></p>
                                                        <div class="border p-2 rounded bg-white">
                                                            <p class="text-muted mb-0">{{ $value }}</p>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row mt-3">
                                            <div class="col-12 text-end">
                                                @if($letter->status == 'approved' || $letter->status == 'printed')
                                                <a href="{{ route('admin.filled-letters.docx', $letter->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

<!-- Modal Konfirmasi Perubahan Status -->
<div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-labelledby="confirmStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmStatusModalLabel">Konfirmasi Perubahan Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengubah status surat ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmStatusBtn">Ya, Ubah</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catatan Admin -->
<div class="modal fade" id="adminNoteModal" tabindex="-1" aria-labelledby="adminNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminNoteModalLabel">Catatan Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="catatanAdminInput" class="form-label">Masukkan catatan admin (wajib untuk status ditolak):</label>
                    <textarea class="form-control" id="catatanAdminInput" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitAdminNoteBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('jQuery loaded and ready');
        console.log('Letter rows found:', $('.letter-row').length);
        console.log('Detail rows found:', $('.letter-details').length);

        // Function untuk toggle detail row
        function toggleLetterDetails(letterRow) {
            const letterId = letterRow.data('letter-id');
            const detailsRow = $('#details-' + letterId);

            console.log('Toggle details for letter ID:', letterId);
            console.log('Details row found:', detailsRow.length);

            // Toggle visibility dari detail row
            if (detailsRow.is(':visible')) {
                detailsRow.hide();
                letterRow.removeClass('table-active');
                console.log('Hiding details for letter:', letterId);
            } else {
                // Sembunyikan semua detail rows yang lain
                $('.letter-details').hide();
                $('.letter-row').removeClass('table-active');

                // Tampilkan detail row yang diklik
                detailsRow.show();
                letterRow.addClass('table-active');
                console.log('Showing details for letter:', letterId);
            }
        }

        // Handle click pada baris surat (tr)
        $('.letter-row').click(function(e) {
            console.log('Row clicked:', $(this).data('letter-id'));
            // Jangan trigger jika yang diklik adalah tombol aksi
            if ($(e.target).closest('a').length > 0) {
                console.log('Click on action button, ignoring');
                return;
            }
            toggleLetterDetails($(this));
        });

        // Handle click pada setiap cell (td) dalam baris
        $('.letter-row td').click(function(e) {
            console.log('Cell clicked');
            // Jangan trigger jika yang diklik adalah tombol aksi
            if ($(e.target).closest('a').length > 0) {
                console.log('Click on action button in cell, ignoring');
                return;
            }

            // Cari parent row dan toggle detail
            const letterRow = $(this).closest('.letter-row');
            toggleLetterDetails(letterRow);

            // Stop propagation untuk mencegah double trigger
            e.stopPropagation();
        });

        // Hover effect untuk menunjukkan bahwa baris bisa diklik
        $('.letter-row').hover(
            function() {
                $(this).addClass('table-hover-custom');
            },
            function() {
                $(this).removeClass('table-hover-custom');
            }
        );

        // Hover effect untuk setiap cell
        $('.letter-row td').hover(
            function() {
                // Jangan tambahkan hover jika ada tombol aksi
                if ($(this).find('a').length === 0) {
                    $(this).closest('.letter-row').addClass('table-hover-custom');
                }
            },
            function() {
                $(this).closest('.letter-row').removeClass('table-hover-custom');
            }
        );

        // Handle perubahan status dropdown
        let currentStatusSelect; // Variabel untuk menyimpan elemen select yang sedang aktif
        let statusChangeConfirmed = false; // Flag untuk menandai apakah perubahan status sudah dikonfirmasi

        $('.status-select').change(function(e) {
            e.stopPropagation(); // Mencegah trigger dropdown detail

            currentStatusSelect = $(this); // Simpan elemen select yang sedang diubah
            const letterId = currentStatusSelect.data('letter-id');
            const newStatus = currentStatusSelect.val();
            const currentStatus = currentStatusSelect.data('current-status');

            console.log('Status change:', letterId, 'from', currentStatus, 'to', newStatus);

            // Jika status tidak berubah, tidak perlu update
            if (newStatus === currentStatus) {
                return;
            }

            // Reset flag konfirmasi
            statusChangeConfirmed = false;
            
            // Tampilkan modal konfirmasi
            $('#confirmStatusModal').modal('show');
        });

        // Handle klik tombol 'Ya, Ubah' di modal konfirmasi
        $('#confirmStatusBtn').click(function() {
            statusChangeConfirmed = true; // Set flag bahwa perubahan sudah dikonfirmasi
            $('#confirmStatusModal').modal('hide'); // Sembunyikan modal konfirmasi

            const letterId = currentStatusSelect.data('letter-id');
            const newStatus = currentStatusSelect.val();
            const currentStatus = currentStatusSelect.data('current-status');

            // Validasi catatan admin untuk status rejected
            if (newStatus === 'rejected') {
                $('#catatanAdminInput').val(''); // Kosongkan input catatan admin
                $('#adminNoteModal').modal('show'); // Tampilkan modal catatan admin
            } else {
                // Jika bukan rejected, langsung kirim AJAX request
                updateLetterStatus(letterId, newStatus, currentStatus, '');
            }
        });

        // Handle klik tombol 'Simpan' di modal catatan admin
        $('#submitAdminNoteBtn').click(function() {
            const catatanAdmin = $('#catatanAdminInput').val();
            if (!catatanAdmin || catatanAdmin.trim() === '') {
                alert('Catatan admin wajib diisi untuk status ditolak!');
                return;
            }

            statusChangeConfirmed = true; // Set flag bahwa perubahan sudah dikonfirmasi
            $('#adminNoteModal').modal('hide'); // Sembunyikan modal catatan admin

            const letterId = currentStatusSelect.data('letter-id');
            const newStatus = currentStatusSelect.val();
            const currentStatus = currentStatusSelect.data('current-status');

            updateLetterStatus(letterId, newStatus, currentStatus, catatanAdmin);
        });

        // Handle tombol 'Batal' di modal konfirmasi dan catatan admin
        $('#confirmStatusModal').on('hidden.bs.modal', function () {
            if (currentStatusSelect && !statusChangeConfirmed) {
                const currentStatus = currentStatusSelect.data('current-status');
                currentStatusSelect.val(currentStatus); // Reset ke status sebelumnya jika dibatalkan
            }
        });

        $('#adminNoteModal').on('hidden.bs.modal', function () {
            if (currentStatusSelect && !statusChangeConfirmed) {
                const currentStatus = currentStatusSelect.data('current-status');
                currentStatusSelect.val(currentStatus); // Reset ke status sebelumnya jika dibatalkan
            }
        });

        // Fungsi untuk mengirim AJAX request update status
        function updateLetterStatus(letterId, newStatus, currentStatus, catatanAdmin) {
            const statusBadge = currentStatusSelect.siblings('.status-badge');

            // Disable dropdown sementara
            currentStatusSelect.prop('disabled', true);

            // Kirim AJAX request
            $.ajax({
                url: '{{ route("admin.filled-letters.update-status", ":id") }}'.replace(':id', letterId),
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: newStatus,
                    catatan_admin: catatanAdmin
                },
                success: function(response) {
                    console.log('Status updated successfully');

                    // Update badge status
                    let badgeClass = '';
                    let badgeText = '';

                    switch (newStatus) {
                        case 'pending':
                            badgeClass = 'bg-warning text-dark';
                            badgeText = 'Pending';
                            break;
                        case 'approved':
                            badgeClass = 'bg-success';
                            badgeText = 'Disetujui';
                            break;
                        case 'rejected':
                            badgeClass = 'bg-danger';
                            badgeText = 'Ditolak';
                            break;
                        case 'printed':
                            badgeClass = 'bg-primary';
                            badgeText = 'Dicetak';
                            break;
                    }

                    // Update badge di detail (status-badge)
                    statusBadge.html('<span class="badge ' + badgeClass + '">' + badgeText + '</span>');

                    // Update badge status di tabel utama
                    const mainTableRow = $('.letter-row[data-letter-id="' + letterId + '"]');
                    const mainStatusCell = mainTableRow.find('td:nth-child(5)'); // Kolom status adalah kolom ke-5
                    mainStatusCell.html('<span class="badge ' + badgeClass + '">' + badgeText + '</span>');

                    // Update tombol download DOCX di kolom aksi (kolom ke-7)
                    const actionCell = mainTableRow.find('td:nth-child(7)');
                    if (newStatus === 'approved' || newStatus === 'printed') {
                        // Tampilkan tombol download jika status approved atau printed
                        const downloadUrl = '{{ route("admin.filled-letters.docx", ":id") }}'.replace(':id', letterId);
                        actionCell.html('<a href="' + downloadUrl + '" class="btn btn-sm btn-primary" title="Download DOCX" onclick="event.stopPropagation();"><i class="bi bi-download"></i></a>');
                    } else {
                        // Sembunyikan tombol download untuk status lainnya
                        actionCell.html('');
                    }

                    // Update tombol download di bagian detail informasi pemohon
                    const detailsRow = $('#details-' + letterId);
                    const downloadButtonContainer = detailsRow.find('.col-12.text-end');
                    if (newStatus === 'approved' || newStatus === 'printed') {
                        // Tampilkan tombol download di detail
                        const downloadUrl = '{{ route("admin.filled-letters.docx", ":id") }}'.replace(':id', letterId);
                        downloadButtonContainer.html('<a href="' + downloadUrl + '" class="btn btn-sm btn-primary"><i class="bi bi-download"></i> Download</a>');
                    } else {
                        // Sembunyikan tombol download di detail
                        downloadButtonContainer.html('');
                    }

                    // Update data-current-status untuk semua dropdown dengan letter-id yang sama
                    $('.status-select[data-letter-id="' + letterId + '"]').data('current-status', newStatus);
                    
                    // Update semua dropdown dengan letter-id yang sama ke status baru
                    $('.status-select[data-letter-id="' + letterId + '"]').val(newStatus);

                    // Reset flag konfirmasi setelah berhasil update
                    statusChangeConfirmed = false;

                    // Show success message
                    alert('Status surat berhasil diperbarui!');
                },
                error: function(xhr) {
                    console.error('Error updating status:', xhr);

                    // Reset dropdown ke status sebelumnya
                    $('.status-select[data-letter-id="' + letterId + '"]').val(currentStatus);

                    // Reset flag konfirmasi setelah error
                    statusChangeConfirmed = false;

                    // Show error message
                    let errorMessage = 'Gagal mengubah status surat.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage += '\n' + Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    // Re-enable dropdown
                    currentStatusSelect.prop('disabled', false);
                }
            });
        }

        // Mencegah dropdown status memicu toggle detail saat diklik
        $('.status-select').click(function(e) {
            e.stopPropagation();
        });
    });
</script>

<style>
    .table-hover-custom {
        background-color: #f8f9fa !important;
    }

    .letter-row:hover {
        background-color: #e9ecef !important;
    }

    .letter-row.table-active {
        background-color: #cfe2ff !important;
    }

    .letter-row td {
        cursor: pointer;
        transition: background-color 0.2s ease;
        position: relative;
    }

    .letter-row td:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Khusus untuk kolom aksi, jangan ubah cursor */
    .letter-row td:last-child {
        cursor: default;
    }

    .letter-row td:last-child:hover {
        background-color: transparent !important;
    }

    /* Style untuk tombol aksi agar tetap terlihat normal */
    .letter-row td:last-child a {
        cursor: pointer;
    }

    .letter-details .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .letter-details .card-body {
        padding: 1.5rem;
    }

    .letter-row {
        transition: background-color 0.2s ease;
    }

    /* Tambahan visual indicator */
    .letter-row td:not(:last-child)::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: transparent;
        transition: background-color 0.2s ease;
        pointer-events: none;
    }

    .letter-row:hover td:not(:last-child)::before {
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endpush
