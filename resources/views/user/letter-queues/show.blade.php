@extends('user.layouts.app')

@section('title', 'Detail Antrian Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">Detail Antrian Surat</h4>
                    <a href="{{ route('user.letter-queues.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informasi Surat</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Jenis Surat</th>
                                        <td>{{ $queue->filledLetter->letterType->nama_jenis }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status Surat</th>
                                        <td>
                                            @if($queue->filledLetter->status == 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif($queue->filledLetter->status == 'approved')
                                                <span class="badge bg-success">Disetujui</span>
                                            @elseif($queue->filledLetter->status == 'dicetak')
                                                <span class="badge bg-primary">Dicetak</span>
                                            @elseif($queue->filledLetter->status == 'rejected')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pengajuan</th>
                                        <td>{{ $queue->filledLetter->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @if($queue->filledLetter->no_surat)
                                    <tr>
                                        <th>Nomor Surat</th>
                                        <td>{{ $queue->filledLetter->no_surat }}/{{ $queue->filledLetter->kode_surat }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informasi Antrian</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Status Antrian</th>
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
                                    <tr>
                                        <th>Jadwal Proses</th>
                                        <td>{{ $queue->scheduled_date->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Waktu Proses</th>
                                        <td>{{ $queue->processing_time }} menit</td>
                                    </tr>
                                    @if($queue->notes)
                                    <tr>
                                        <th>Catatan</th>
                                        <td>{{ $queue->notes }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('user.letters.show', $queue->filledLetter->id) }}" class="btn btn-primary">
                        <i class="fa fa-file-alt"></i> Lihat Detail Surat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection