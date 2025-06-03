@extends('user.layouts.app')

@section('title', 'Daftar Jenis Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h4 class="mb-4">Daftar Jenis Surat</h4>
                <p>Silakan pilih jenis surat yang ingin Anda ajukan:</p>

                <div class="row mt-4">
                    @foreach($letterTypes as $type)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">{{ $type->nama_jenis }}</h5>
                                <p class="card-text">{{ $type->deskripsi ?? 'Surat untuk keperluan administrasi' }}</p>
                                <a href="{{ route('user.letters.create', $type->id) }}" class="btn btn-primary">Buat Surat</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(count($letterTypes) == 0)
                <div class="alert alert-info">
                    Belum ada jenis surat yang tersedia. Silakan hubungi admin.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection