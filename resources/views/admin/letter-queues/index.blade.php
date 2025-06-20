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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queues as $index => $queue)
                        <tr class="queue-row" data-queue-id="{{ $queue->id }}" style="cursor: pointer;">
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

                        </tr>
                        <tr class="queue-details" id="details-{{ $queue->id }}" style="display: none;">
                            <td colspan="5">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">Informasi Antrian</h6>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th width="40%">Pemohon:</th>
                                                        <td>{{ $queue->filledLetter->user->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jenis Surat:</th>
                                                        <td>{{ $queue->filledLetter->letterType->nama_jenis }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Jadwal:</th>
                                                        <td>{{ $queue->scheduled_date->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary mb-3">Status & Catatan</h6>
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th width="40%">Status:</th>
                                                        <td>
                                                            <select class="form-select form-select-sm status-select" data-queue-id="{{ $queue->id }}" data-current-status="{{ $queue->status }}" style="width: auto; display: inline-block;">
                                                                <option value="waiting" {{ $queue->status == 'waiting' ? 'selected' : '' }}>Menunggu</option>
                                                                <option value="processing" {{ $queue->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                                                                <option value="completed" {{ $queue->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                                            </select>
                                                            <span class="status-badge ms-2">
                                                                @if($queue->status == 'waiting')
                                                                <span class="badge bg-warning">Menunggu</span>
                                                                @elseif($queue->status == 'processing')
                                                                <span class="badge bg-primary">Diproses</span>
                                                                @elseif($queue->status == 'completed')
                                                                <span class="badge bg-success">Selesai</span>
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    @if($queue->notes)
                                                    <tr>
                                                        <th>Catatan:</th>
                                                        <td>{{ $queue->notes }}</td>
                                                    </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada antrian surat</td>
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

<!-- Modal Konfirmasi Perubahan Status Antrian -->
<div class="modal fade" id="confirmQueueStatusModal" tabindex="-1" aria-labelledby="confirmQueueStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmQueueStatusModalLabel">Konfirmasi Perubahan Status Antrian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengubah status antrian ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmQueueStatusBtn">Ya, Ubah</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Catatan Antrian (Opsional) -->
<div class="modal fade" id="queueNoteModal" tabindex="-1" aria-labelledby="queueNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="queueNoteModalLabel">Catatan Antrian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="queueNoteInput" class="form-label">Masukkan catatan (opsional):</label>
                    <textarea class="form-control" id="queueNoteInput" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitQueueNoteBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    $(document).ready(function() {
        console.log('jQuery loaded and ready');
        console.log('Queue rows found:', $('.queue-row').length);
        console.log('Detail rows found:', $('.queue-details').length);

        // Function untuk toggle detail row
        function toggleQueueDetails(queueId) {
            const detailRow = $('#details-' + queueId);
            const queueRow = $('.queue-row[data-queue-id="' + queueId + '"]');

            if (detailRow.is(':visible')) {
                detailRow.hide();
                queueRow.removeClass('table-active');
            } else {
                // Sembunyikan semua detail row lainnya
                $('.queue-details').hide();
                $('.queue-row').removeClass('table-active');

                // Tampilkan detail row yang diklik
                detailRow.show();
                queueRow.addClass('table-active');
            }
        }

        // Event handler untuk klik pada baris antrian
        $('.queue-row').click(function(e) {

            const queueId = $(this).data('queue-id');
            console.log('Queue row clicked:', queueId);
            toggleQueueDetails(queueId);
        });

        // Hover effect untuk menunjukkan bahwa baris bisa diklik
        $('.queue-row').hover(
            function() {
                $(this).addClass('table-hover-custom');
            },
            function() {
                $(this).removeClass('table-hover-custom');
            }
        );

        // Hover effect untuk setiap cell
        $('.queue-row td').hover(
            function() {
                // Jangan tambahkan hover jika ada tombol aksi
                if ($(this).find('a').length === 0) {
                    $(this).closest('.queue-row').addClass('table-hover-custom');
                }
            },
            function() {
                $(this).closest('.queue-row').removeClass('table-hover-custom');
            }
        );

        // Handle perubahan status dropdown
        let currentQueueStatusSelect; // Variabel untuk menyimpan elemen select yang sedang aktif
        let queueStatusChangeConfirmed = false; // Flag untuk menandai apakah perubahan status sudah dikonfirmasi

        $('.status-select').change(function(e) {
            e.stopPropagation(); // Mencegah trigger dropdown detail

            currentQueueStatusSelect = $(this); // Simpan elemen select yang sedang diubah
            const queueId = currentQueueStatusSelect.data('queue-id');
            const newStatus = currentQueueStatusSelect.val();
            const currentStatus = currentQueueStatusSelect.data('current-status');

            console.log('Status change:', queueId, 'from', currentStatus, 'to', newStatus);

            // Jika status tidak berubah, tidak perlu update
            if (newStatus === currentStatus) {
                return;
            }

            // Reset flag konfirmasi
            queueStatusChangeConfirmed = false;
            
            // Tampilkan modal konfirmasi
            $('#confirmQueueStatusModal').modal('show');
        });

        // Handle klik tombol 'Ya, Ubah' di modal konfirmasi
        $('#confirmQueueStatusBtn').click(function() {
            queueStatusChangeConfirmed = true; // Set flag bahwa perubahan sudah dikonfirmasi
            $('#confirmQueueStatusModal').modal('hide'); // Sembunyikan modal konfirmasi

            const queueId = currentQueueStatusSelect.data('queue-id');
            const newStatus = currentQueueStatusSelect.val();
            const currentStatus = currentQueueStatusSelect.data('current-status');

            // Validasi catatan untuk status completed
            if (newStatus === 'completed') {
                $('#queueNoteInput').val(''); // Kosongkan input catatan
                $('#queueNoteModal').modal('show'); // Tampilkan modal catatan
            } else {
                // Jika bukan completed, langsung kirim AJAX request
                updateQueueStatus(queueId, newStatus, currentStatus, '');
            }
        });

        // Handle klik tombol 'Simpan' di modal catatan antrian
        $('#submitQueueNoteBtn').click(function() {
            const notes = $('#queueNoteInput').val();
            // Catatan opsional, jadi tidak perlu validasi wajib isi

            queueStatusChangeConfirmed = true; // Set flag bahwa perubahan sudah dikonfirmasi
            $('#queueNoteModal').modal('hide'); // Sembunyikan modal catatan

            const queueId = currentQueueStatusSelect.data('queue-id');
            const newStatus = currentQueueStatusSelect.val();
            const currentStatus = currentQueueStatusSelect.data('current-status');

            updateQueueStatus(queueId, newStatus, currentStatus, notes);
        });

        // Handle tombol 'Batal' di modal konfirmasi dan catatan antrian
        $('#confirmQueueStatusModal').on('hidden.bs.modal', function () {
            if (currentQueueStatusSelect && !queueStatusChangeConfirmed) {
                const currentStatus = currentQueueStatusSelect.data('current-status');
                currentQueueStatusSelect.val(currentStatus); // Reset ke status sebelumnya jika dibatalkan
            }
        });

        $('#queueNoteModal').on('hidden.bs.modal', function () {
            if (currentQueueStatusSelect && !queueStatusChangeConfirmed) {
                const currentStatus = currentQueueStatusSelect.data('current-status');
                currentQueueStatusSelect.val(currentStatus); // Reset ke status sebelumnya jika dibatalkan
            }
        });

        // Fungsi untuk mengirim AJAX request update status
        function updateQueueStatus(queueId, newStatus, currentStatus, notes) {
            const statusBadge = currentQueueStatusSelect.siblings('.status-badge');

            // Disable dropdown sementara
            currentQueueStatusSelect.prop('disabled', true);

            // Kirim AJAX request
            $.ajax({
                url: '{{ route("admin.letter-queues.update-status", ":id") }}'.replace(':id', queueId),
                method: 'PUT',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: newStatus,
                    notes: notes
                },
                success: function(response) {
                    console.log('Status updated successfully');

                    // Update badge status
                    let badgeClass = '';
                    let badgeText = '';

                    switch (newStatus) {
                        case 'waiting':
                            badgeClass = 'bg-warning';
                            badgeText = 'Menunggu';
                            break;
                        case 'processing':
                            badgeClass = 'bg-primary';
                            badgeText = 'Diproses';
                            break;
                        case 'completed':
                            badgeClass = 'bg-success';
                            badgeText = 'Selesai';
                            break;
                    }

                    statusBadge.html('<span class="badge ' + badgeClass + '">' + badgeText + '</span>');

                    // Update badge di baris utama juga
                    $('.queue-row[data-queue-id="' + queueId + '"] td:nth-child(5)').html('<span class="badge ' + badgeClass + '">' + badgeText + '</span>');

                    // Update data-current-status
                    $('.status-select[data-queue-id="' + queueId + '"]').data('current-status', newStatus);

                    // Reset flag konfirmasi setelah berhasil update
                    queueStatusChangeConfirmed = false;

                    // Show success message
                    alert('Status antrian berhasil diperbarui!');
                },
                error: function(xhr) {
                    console.error('Error updating status:', xhr);

                    // Reset dropdown ke status sebelumnya
                    $('.status-select[data-queue-id="' + queueId + '"]').val(currentStatus);

                    // Reset flag konfirmasi setelah error
                    queueStatusChangeConfirmed = false;

                    // Show error message
                    let errorMessage = 'Gagal mengubah status antrian.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage += '\n' + Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    // Re-enable dropdown
                    currentQueueStatusSelect.prop('disabled', false);
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

    .queue-row:hover {
        background-color: #e9ecef !important;
    }

    .queue-row.table-active {
        background-color: #cfe2ff !important;
    }

    .queue-row td {
        cursor: pointer;
        transition: background-color 0.2s ease;
        position: relative;
    }

    .queue-row td:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }



    .queue-details .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .queue-details .card-body {
        padding: 1.5rem;
    }

    .queue-row {
        transition: background-color 0.2s ease;
    }

    /* Tambahan visual indicator */
    .queue-row td::before {
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

    .queue-row:hover td::before {
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endpush