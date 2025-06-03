@extends('user.layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Announcement Modal -->
@if(count($data['activeAnnouncements']) > 0)
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="announcementModalLabel">Pengumuman</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="announcementCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="false">
                    <div class="carousel-inner">
                        @foreach($data['activeAnnouncements'] as $index => $announcement)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="announcement-item">
                                <h4>{{ $announcement->title }}</h4>
                                <div class="announcement-date text-muted small mb-2">
                                    Diposting: {{ $announcement->created_at->format('d M Y H:i') }}
                                </div>
                                <div class="announcement-content">
                                    {!! nl2br(e($announcement->content)) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if(count($data['activeAnnouncements']) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <div class="carousel-indicators position-relative mt-2">
                        @foreach($data['activeAnnouncements'] as $index => $announcement)
                        <button type="button" data-bs-target="#announcementCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }} bg-dark"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="dontShowAgain">Jangan Tampilkan Lagi Hari Ini</button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Announcement Button -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <button class="btn btn-primary rounded-circle shadow" id="showAnnouncement" style="width: 50px; height: 50px;">
        <i class="bi bi-megaphone-fill"></i>
    </button>
</div>
@endif

<!-- Pengumuman Jadwal Dijeda -->
<div class="container-fluid pt-4 px-4">
    @if(count($data['pausedSchedules']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($data['pausedSchedules'] as $schedule)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Pengumuman:</strong> {{ $schedule->pause_message }}
                <p class="mb-0"><small>Jadwal pelayanan {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} sedang dijeda. Antrian yang terjadwal pada jam tersebut telah disesuaikan.</small></p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-file-alt fa-3x text-primary"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Surat</p>
                    <h6 class="mb-0">{{ $data['totalLetters'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-clock fa-3x text-warning"></i>
                <div class="ms-3">
                    <p class="mb-2">Menunggu</p>
                    <h6 class="mb-0">{{ $data['pendingLetters'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-check-circle fa-3x text-success"></i>
                <div class="ms-3">
                    <p class="mb-2">Disetujui</p>
                    <h6 class="mb-0">{{ $data['approvedLetters'] }}</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4">
                <i class="fa fa-hourglass-half fa-3x text-info"></i>
                <div class="ms-3">
                    <p class="mb-2">Jadwal Antrian</p>
                    @if($data['nextQueuedLetter'])
                    <h6 class="mb-0">{{ $data['nextQueuedLetter']->scheduled_date->format('d/m/Y H:i') }}</h6>
                    <small>{{ $data['nextQueuedLetter']->filledLetter->letterType->nama_jenis }}</small>
                    @else
                    <h6 class="mb-0">Tidak ada</h6>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h4 class="mb-4">Surat Terbaru</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Jenis Surat</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recentLetters'] as $index => $letter)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $letter->letterType->nama_jenis }}</td>
                                <td>{{ $letter->created_at->format('d-m-Y') }}</td>
                                <td>
                                    @if($letter->status == 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                    @elseif($letter->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                    @elseif($letter->status == 'dicetak')
                                    <span class="badge bg-primary">Dicetak</span>
                                    @elseif($letter->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('user.letters.show', $letter->id) }}" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada surat yang diajukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('user.letters.history') }}" class="btn btn-primary">Lihat Semua Surat</a>
                    <a href="{{ route('user.letters.index') }}" class="btn btn-success">Ajukan Surat Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek apakah ada pengumuman aktif
        @if(count($data['activeAnnouncements']) > 0)
        // Cek apakah user sudah menutup pengumuman hari ini
        const lastClosed = localStorage.getItem('announcement_closed');
        const today = new Date().toDateString();

        if (lastClosed !== today) {
            // Tampilkan modal pengumuman secara otomatis
            const announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
            announcementModal.show();
        }

        // Event listener untuk tombol jangan tampilkan lagi
        document.getElementById('dontShowAgain').addEventListener('click', function() {
            localStorage.setItem('announcement_closed', today);
            bootstrap.Modal.getInstance(document.getElementById('announcementModal')).hide();
        });

        // Event listener untuk tombol floating
        document.getElementById('showAnnouncement').addEventListener('click', function() {
            const announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
            announcementModal.show();
        });
        @endif
    });
</script>
@endsection