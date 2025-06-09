@extends('admin.layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h4 class="mb-4">Detail Surat {{ $letter->letterType->name }}</h4>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi Surat</h5>
                        <span class="badge {{ $letter->status == 'pending' ? 'bg-warning' : ($letter->status == 'approved' || $letter->status == 'printed' ? 'bg-success' : 'bg-danger') }}">
                            {{ $letter->status == 'pending' ? 'Menunggu' : ($letter->status == 'approved' ? 'Disetujui' : ($letter->status == 'printed' ? 'Dicetak' : 'Ditolak')) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Jenis Surat</div>
                            <div class="col-md-8">{{ $letter->letterType->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Tanggal Pengajuan</div>
                            <div class="col-md-8">{{ $letter->created_at->format('d-m-Y H:i') }}</div>
                        </div>
                        @if($letter->no_surat)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Nomor Surat</div>
                            <div class="col-md-8">{{ $letter->no_surat }}</div>
                        </div>
                        @endif
                        @if($letter->catatan_admin)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Catatan Admin</div>
                            <div class="col-md-8">{{ $letter->catatan_admin }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Data Surat</h5>
                    </div>
                    <div class="card-body">
                        @php
                        $filledData = is_array($letter->filled_data) ? $letter->filled_data : json_decode($letter->filled_data, true);
                        @endphp

                        @foreach($letter->letterType->dataItems as $item)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">{{ $item->label }}</div>
                            <div class="col-md-8">{{ $filledData[$item->key] ?? '-' }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.letters.history') }}" class="btn btn-secondary">Kembali</a>

                    @if($letter->status == 'rejected')
                    <a href="{{ route('admin.letters.edit', $letter->id) }}" class="btn btn-warning">Edit Surat</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
