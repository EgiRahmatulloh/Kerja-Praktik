@extends('admin.layouts.app')

@section('title', 'Tambah Variabel Surat')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Tambah Variabel Surat</h5>
            <a href="{{ route('admin.data-items.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.data-items.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="key" class="form-label">Kunci Variabel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('key') is-invalid @enderror" id="key" name="key" value="{{ old('key') }}" required>
                            @error('key')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="form-text">
                                Gunakan format snake_case (mis: nama_lengkap, tempat_lahir)
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label" value="{{ old('label') }}" required>
                            @error('label')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                            <div class="form-text">
                                Label yang akan ditampilkan pada form (mis: Nama Lengkap, Tempat Lahir). Awal kalimat harus menggunakan huruf kapital.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="input_type" class="form-label">Tipe Input <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipe_input') is-invalid @enderror" id="input_type" name="tipe_input" required>
                                <option value="">Pilih Tipe Input</option>
                                <option value="text" {{ old('input_type') == 'text' ? 'selected' : '' }}>Text</option>
                                <option value="textarea" {{ old('input_type') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                <option value="date" {{ old('input_type') == 'date' ? 'selected' : '' }}>Date</option>
                                <option value="select" {{ old('input_type') == 'select' ? 'selected' : '' }}>Select</option>
                            </select>
                            @error('tipe_input')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="required" class="form-label">Wajib Diisi</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="required" name="required" value="1" {{ old('required') ? 'checked' : '' }}>
                                <label class="form-check-label" for="required">
                                    Ya, variabel ini wajib diisi
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3" id="options-container" style="{{ old('tipe_input') == 'select' ? '' : 'display: none;' }}">
                    <label for="options" class="form-label">Opsi Pilihan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('opsi') is-invalid @enderror" id="options" name="opsi" rows="3">{{ old('opsi') }}</textarea>
                    @error('opsi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="form-text">
                        Pisahkan opsi dengan koma (mis: Laki-laki, Perempuan)
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="help_text" class="form-label">Teks Bantuan</label>
                    <input type="text" class="form-control @error('help_text') is-invalid @enderror" id="help_text" name="help_text" value="{{ old('help_text') }}">
                    @error('help_text')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                    <div class="form-text">
                        Teks bantuan yang akan ditampilkan di bawah input. Awal kalimat harus menggunakan huruf kapital.
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.data-items.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputType = document.getElementById('input_type');
        const optionsContainer = document.getElementById('options-container');
        const labelInput = document.getElementById('label');
        const helpTextInput = document.getElementById('help_text');

        // Validasi untuk memastikan awal kalimat menggunakan huruf kapital pada label
        labelInput.addEventListener('blur', function() {
            if (this.value.length > 0) {
                // Mengubah karakter pertama menjadi huruf kapital
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });

        // Validasi untuk memastikan awal kalimat menggunakan huruf kapital pada help_text
        helpTextInput.addEventListener('blur', function() {
            if (this.value.length > 0) {
                // Mengubah karakter pertama menjadi huruf kapital
                this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });

        inputType.addEventListener('change', function() {
            if (this.value === 'select') {
                optionsContainer.style.display = 'block';
                document.getElementById('options').setAttribute('required', 'required');
            } else {
                optionsContainer.style.display = 'none';
                document.getElementById('options').removeAttribute('required');
            }
        });
    });
</script>
@endsection