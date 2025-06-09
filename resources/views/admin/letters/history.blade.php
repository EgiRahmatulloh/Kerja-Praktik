@extends('admin.layouts.app')

@section('title', 'Riwayat Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h4 class="mb-4">Riwayat Pengajuan Surat</h4>

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Jenis Surat</th>
                                <th scope="col">Tanggal Pengajuan</th>
                                <th scope="col">Status</th>
                                <th scope="col">No. Surat</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($letters as $index => $letter)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $letter->letterType->nama_jenis }}</td>
                                <td>{{ $letter->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    @if($letter->status == 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                    @elseif($letter->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                    @elseif($letter->status == 'printed')
                                    <span class="badge bg-primary">Dicetak</span>
                                    @elseif($letter->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $letter->no_surat ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.letters.show', $letter->id) }}" class="btn btn-sm btn-info">Detail</a>

                                    @if($letter->status == 'rejected' || $letter->status == 'completed')
                                    <a href="{{ route('admin.letters.edit', $letter->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endif

                                    @if($letter->status == 'completed' || $letter->status == 'printed')
                                    <a href="{{ route('admin.filled-letters.print', $letter->id) }}" class="btn btn-sm btn-success" title="Generate DOCX">
                                        <i class="fa fa-file-word"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada surat yang diajukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $letters->links() }}
                </div>

                <div class="mt-3">
                    <a href="{{ route('admin.letters.index') }}" class="btn btn-primary">Buat Surat Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
