@extends('user.layouts.app')

@section('title', 'Antrian Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h4 class="mb-4">Antrian Surat</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Surat</th>
                                <th>Jadwal Proses</th>
                                <th>Waktu Proses</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($queues as $index => $queue)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $queue->filledLetter->letterType->nama_jenis }}</td>
                                <td>{{ $queue->scheduled_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $queue->processing_time }} menit</td>
                                <td>
                                    @if($queue->status == 'waiting')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($queue->status == 'processing')
                                        <span class="badge bg-primary">Diproses</span>
                                    @elseif($queue->status == 'completed')
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('user.letter-queues.show', $queue->id) }}" class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada antrian surat</td>
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
</div>
@endsection