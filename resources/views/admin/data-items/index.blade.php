@extends('admin.layouts.app')

@section('title', 'Manajemen Variabel Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Variabel Surat</h5>
            <div>
                <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#systemVariablesModal">
                    <i class="bi bi-info-circle"></i> Variabel Sistem
                </button>
                <a href="{{ route('admin.data-items.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Variabel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kunci</th>
                            <th>Label</th>
                            <th>Tipe Input</th>
                            <th>Wajib</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $item->key }}</code></td>
                            <td>{{ $item->label }}</td>
                            <td>{{ $item->tipe_input }}</td>
                            <td>{!! $item->required ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}</td>
                            <td>
                                <a href="{{ route('admin.data-items.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.data-items.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus variabel ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada variabel surat</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $dataItems->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Variabel Sistem -->
<div class="modal fade" id="systemVariablesModal" tabindex="-1" aria-labelledby="systemVariablesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="systemVariablesModalLabel">Variabel Sistem Otomatis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Variabel berikut diisi otomatis oleh sistem saat mencetak surat:</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Variabel</th>
                                <th>Deskripsi</th>
                                <th>Contoh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>${noSurat}</code><br><code>${data.noSurat}</code></td>
                                <td>Nomor surat yang diset admin</td>
                                <td>001/KEL/2024</td>
                            </tr>
                            <tr>
                                <td><code>${tglSurat}</code><br><code>${data.tglSurat}</code></td>
                                <td>Tanggal surat (format: YYYY-MM-DD)</td>
                                <td>2024-01-15</td>
                            </tr>
                            <tr>
                                <td><code>${formattedDate}</code><br><code>${data.formattedDate}</code></td>
                                <td>Tanggal terformat (dd MMM yyyy)</td>
                                <td>15 Jan 2024</td>
                            </tr>
                            <tr>
                                <td><code>${bulan}</code><br><code>${data.bulan}</code></td>
                                <td>Bulan saat ini (format angka)</td>
                                <td>01</td>
                            </tr>
                            <tr>
                                <td><code>${bulanHuruf}</code><br><code>${data.bulanHuruf}</code></td>
                                <td>Bulan saat ini (format huruf)</td>
                                <td>Januari</td>
                            </tr>
                            <tr>
                                <td><code>${tahun}</code><br><code>${data.tahun}</code></td>
                                <td>Tahun saat ini</td>
                                <td>2024</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="bi bi-lightbulb"></i>
                    <strong>Tips:</strong> Gunakan variabel ini di template DOCX Anda. Sistem akan otomatis menggantinya dengan nilai yang sesuai saat mencetak surat.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection