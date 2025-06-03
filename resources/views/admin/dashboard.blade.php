@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Template</h6>
                            <h2 class="mb-0">{{ $data['totalTemplates'] }}</h2>
                        </div>
                        <i class="bi bi-file-earmark-text fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.templates.index') }}" class="text-white text-decoration-none">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Jenis Surat</h6>
                            <h2 class="mb-0">{{ $data['totalLetterTypes'] }}</h2>
                        </div>
                        <i class="bi bi-file-earmark-richtext fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.letter-types.index') }}" class="text-white text-decoration-none">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Informasi Antrian</h6>
                            @if(isset($data['currentQueue']))
                            <h2 class="mb-0">{{ $data['currentQueue']->filledLetter->user->name }}</h2>
                            @else
                            <h2 class="mb-0">Tidak Ada Antrian</h2>
                            @endif
                        </div>
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    @if(isset($data['currentQueue']))
                    <span class="text-dark">{{ $data['currentQueue']->scheduled_date->format('H:i') }} - {{ $data['currentQueueEndTime']->format('H:i') }}</span>
                    @else
                    @if(isset($data['serviceSchedule']))
                    <span class="text-dark">Jadwal Pelayanan {{ Carbon\Carbon::parse($data['serviceSchedule']->start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($data['serviceSchedule']->end_time)->format('H:i') }}</span>
                    @else
                    <span class="text-dark">Tidak ada jadwal pelayanan</span>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Surat Pending</h6>
                            <h2 class="mb-0">{{ $data['pendingLetters'] }}</h2>
                        </div>
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('admin.filled-letters.index') }}" class="text-white text-decoration-none">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Surat</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-2">
                                <h3>{{ $data['pendingLetters'] }}</h3>
                                <span class="text-muted">Pending</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-2">
                                <h3>{{ $data['approvedLetters'] }}</h3>
                                <span class="text-muted">Disetujui</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded mb-2">
                                <h3>{{ $data['printedLetters'] }}</h3>
                                <span class="text-muted">Dicetak</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pengajuan Surat Terbaru</h5>
                    <a href="{{ route('admin.filled-letters.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pemohon</th>
                                    <th>Jenis Surat</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['recentLetters'] as $letter)
                                <tr>
                                    <td>{{ $letter->user->name }}</td>
                                    <td>{{ $letter->letterType->nama_jenis }}</td>
                                    <td>
                                        @if($letter->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @elseif($letter->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                        @elseif($letter->status == 'dicetak')
                                        <span class="badge bg-primary">Dicetak</span>
                                        @endif
                                    </td>
                                    <td>{{ $letter->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.filled-letters.show', $letter->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada pengajuan surat terbaru</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection