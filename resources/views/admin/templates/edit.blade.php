@extends('admin.layouts.app')

@section('content')
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Edit Template Surat</h6>
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nama_template" class="form-label">Nama Template</label>
                        <input type="text" class="form-control @error('nama_template') is-invalid @enderror" id="nama_template" name="nama_template" value="{{ old('nama_template', $template->nama_template) }}" required>
                        @error('nama_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="file_template" class="form-label">File Template (DOCX) - Biarkan kosong jika tidak ingin mengubah</label>
                        <input type="file" class="form-control @error('file_template') is-invalid @enderror" id="file_template" name="file_template" accept=".docx">
                        @error('file_template')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($template->template_path)
                            <small class="form-text text-muted">File saat ini: <a href="{{ $template->public_url }}" target="_blank">{{ basename($template->template_path) }}</a></small>
                        @endif
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="aktif" name="aktif" value="1" {{ old('aktif', $template->aktif) ? 'checked' : '' }}>
                        <label class="form-check-label" for="aktif">
                            Aktif
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="share_setting" class="form-label">Pengaturan Berbagi</label>
                        <select class="form-select" id="share_setting" name="share_setting">
                            <option value="private" {{ old('share_setting', $template->share_setting) == 'private' ? 'selected' : '' }}>Pribadi (Hanya saya)</option>
                            <option value="public" {{ old('share_setting', $template->share_setting) == 'public' ? 'selected' : '' }}>Publik (Semua admin)</option>
                            <option value="limited" {{ old('share_setting', $template->share_setting) == 'limited' ? 'selected' : '' }}>Terbatas (Pilih admin)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="shared_users_container">
                        <label for="shared_users" class="form-label">Bagikan Kepada Admin Tertentu</label>
                        <select class="form-select" id="shared_users" name="shared_users[]" multiple>
                            @foreach ($admins as $admin)
                                <option value="{{ $admin->id }}" {{ in_array($admin->id, old('shared_users', $sharedUserIds)) ? 'selected' : '' }}>
                                    {{ $admin->name }} ({{ $admin->username }})
                                </option>
                            @endforeach
                        </select>
                        @error('shared_users')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.templates.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shareSettingSelect = document.getElementById('share_setting');
        const sharedUsersContainer = document.getElementById('shared_users_container');

        function toggleSharedUsers() {
            if (shareSettingSelect.value === 'limited') {
                sharedUsersContainer.style.display = 'block';
            } else {
                sharedUsersContainer.style.display = 'none';
            }
        }

        shareSettingSelect.addEventListener('change', toggleSharedUsers);
        toggleSharedUsers(); // Initial call to set visibility based on default/old value
    });
</script>
@endpush
