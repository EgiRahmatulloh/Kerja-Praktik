@extends('admin.layouts.app')

@section('title', 'Jadwal Pelayanan')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Jadwal Pelayanan</h4>
                    <a href="{{ route('admin.service-schedules.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Tambah Jadwal
                    </a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Tampilkan pengumuman jika ada jadwal yang dijeda -->
                @foreach($schedules as $schedule)
                @if($schedule->is_active && $schedule->is_paused && $schedule->pause_message)
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Pengumuman:</strong> {{ $schedule->pause_message }}
                    <p class="mb-0"><small>Jadwal pelayanan {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} sedang dijeda.</small></p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @endforeach

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>Admin/Staf</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Jam Istirahat</th>
                                <th>Lama Proses (menit)</th>
                                <th>Status</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $index => $schedule)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $schedule->user->name ?? 'Tidak diketahui' }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                <td>
                                    @if($schedule->break_start_time && $schedule->break_end_time)
                                        {{ \Carbon\Carbon::parse($schedule->break_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end_time)->format('H:i') }}
                                    @else
                                        <span class="text-muted">Tidak ada</span>
                                    @endif
                                </td>
                                <td>{{ $schedule->processing_time }}</td>
                                <td>
                                    @if($schedule->is_active)
                                    @if($schedule->is_paused)
                                    <span class="badge bg-warning">Dijeda</span>
                                    @else
                                    <span class="badge bg-success">Aktif</span>
                                    @endif
                                    @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.service-schedules.edit', $schedule->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    @if($schedule->is_active && !$schedule->is_paused)
                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#pauseModal{{ $schedule->id }}">
                                        <i class="fa fa-pause"></i>
                                    </button>
                                    @elseif($schedule->is_active && $schedule->is_paused)
                                    <form action="{{ route('admin.service-schedules.unpause', $schedule->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin melanjutkan jadwal ini?')">
                                            <i class="fa fa-play"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('admin.service-schedules.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>

                                    <!-- Modal Jeda Jadwal -->
                                    <div class="modal fade" id="pauseModal{{ $schedule->id }}" tabindex="-1" aria-labelledby="pauseModalLabel{{ $schedule->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.service-schedules.pause', $schedule->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="pauseModalLabel{{ $schedule->id }}">Jeda Jadwal Pelayanan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="pause_message" class="form-label">Pesan Pengumuman</label>
                                                            <textarea class="form-control" id="pause_message" name="pause_message" rows="3" required placeholder="Masukkan pesan pengumuman untuk pengguna"></textarea>
                                                            <small class="form-text text-muted">Pesan ini akan ditampilkan sebagai pengumuman kepada pengguna.</small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="pause_end_time" class="form-label">Waktu Selesai Jeda</label>
                                                            <input type="time" class="form-control" id="pause_end_time" name="pause_end_time" required>
                                                            <small class="form-text text-muted">Masukkan waktu selesai jeda pelayanan (format: HH:MM).</small>
                                                        </div>
                                                        <div class="alert alert-info">
                                                            <small>Menjeda jadwal akan otomatis memindahkan antrian yang terjadwal pada jam ini ke jadwal lain yang aktif atau ke hari berikutnya.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning">Jeda Jadwal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada jadwal pelayanan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection