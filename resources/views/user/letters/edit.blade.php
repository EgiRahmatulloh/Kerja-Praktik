@extends('user.layouts.app')

@section('title', 'Edit Surat')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h4 class="mb-4">Edit Surat {{ $letter->letterType->name }}</h4>

                @if($letter->catatan_admin)
                <div class="alert alert-warning mb-4">
                    <strong>Catatan Admin:</strong> {{ $letter->catatan_admin }}
                </div>
                @endif

                <form action="{{ route('user.letters.update', $letter->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="row">
                        @foreach($letter->letterType->dataItems as $item)
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="{{ $item->key }}">
                                    {{ $item->label }}
                                    @if($item->required)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if($item->tipe_input == 'text')
                                <input type="text" class="form-control @error($item->key) is-invalid @enderror text-input"
                                    id="{{ $item->key }}" name="{{ $item->key }}"
                                    value="{{ old($item->key, $letter->filled_data[$item->key] ?? '') }}"
                                    {{ $item->required ? 'required' : '' }}>
                                @elseif($item->tipe_input == 'date')
                                <input type="date" class="form-control @error($item->key) is-invalid @enderror"
                                    id="{{ $item->key }}" name="{{ $item->key }}"
                                    value="{{ old($item->key, $letter->filled_data[$item->key] ?? '') }}"
                                    {{ $item->required ? 'required' : '' }}>
                                @elseif($item->tipe_input == 'select')
                                <select class="form-select @error($item->key) is-invalid @enderror"
                                    id="{{ $item->key }}" name="{{ $item->key }}"
                                    {{ $item->required ? 'required' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    @foreach(json_decode($item->opsi) as $option)
                                    <option value="{{ $option }}" {{ old($item->key, $letter->filled_data[$item->key] ?? '') == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                    @endforeach
                                </select>
                                @elseif($item->tipe_input == 'textarea')
                                <textarea class="form-control @error($item->key) is-invalid @enderror text-input"
                                    id="{{ $item->key }}" name="{{ $item->key }}"
                                    rows="3" {{ $item->required ? 'required' : '' }}>{{ old($item->key, $letter->filled_data[$item->key] ?? '') }}</textarea>
                                @endif

                                @if($item->help_text)
                                <small class="form-text text-muted">{{ $item->help_text }}</small>
                                @endif

                                @error($item->key)
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                                <small class="form-text text-muted"></small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('user.letters.history') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Ajukan Ulang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mendapatkan semua input teks dan textarea
        const textInputs = document.querySelectorAll('.text-input');

        // Menambahkan event listener untuk setiap input
        textInputs.forEach(function(input) {
            input.addEventListener('blur', function() {
                if (this.value.length > 0) {
                    // Mengubah karakter pertama menjadi huruf kapital
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }
            });
        });
    });
</script>
@endsection