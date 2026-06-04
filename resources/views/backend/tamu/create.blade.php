{{ html()->form('POST', route($page->url.'.store'))
    ->id('form-create-'.$page->code)
    ->acceptsFiles()
    ->class('form form-horizontal')
    ->open() }}

<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">

            {{-- Nama --}}
            <div class="col-md-7">
                <div class="form-group mb-3">
                    {!! html()->label('Nama Lengkap <span class="text-danger">*</span>', 'nama')->class('control-label fw-semibold') !!}
                    {!! html()->text('nama', old('nama'))
                        ->placeholder('Masukkan nama lengkap')
                        ->class('form-control '.($errors->has('nama') ? 'is-invalid' : ''))
                        ->id('nama')
                        ->required() !!}
                    @error('nama')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div class="col-md-5">
                <div class="form-group mb-3">
                    {!! html()->label('Jenis Kelamin <span class="text-danger">*</span>', 'jenis_kelamin')
                        ->class('control-label fw-semibold d-block') !!}
                    <div class="d-flex gap-3 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin"
                                   id="jk_laki_laki" value="Laki-laki"
                                   {{ old('jenis_kelamin') == 'Laki-laki' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jk_laki_laki">👨 Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin"
                                   id="jk_perempuan" value="Perempuan"
                                   {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jk_perempuan">👩 Perempuan</label>
                        </div>
                    </div>
                    @error('jenis_kelamin')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Email', 'email')->class('control-label fw-semibold') !!}
                    {!! html()->email('email', old('email'))
                        ->placeholder('email@contoh.com')
                        ->class('form-control '.($errors->has('email') ? 'is-invalid' : ''))
                        ->id('email') !!}
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- No HP --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('No. HP / Telepon', 'no_hp')->class('control-label fw-semibold') !!}
                    {!! html()->text('no_hp', old('no_hp'))
                        ->placeholder('08xx-xxxx-xxxx')
                        ->class('form-control '.($errors->has('no_hp') ? 'is-invalid' : ''))
                        ->id('no_hp') !!}
                    @error('no_hp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pekerjaan --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Pekerjaan / Instansi', 'pekerjaan')->class('control-label fw-semibold') !!}
                    {!! html()->select(
                            'pekerjaan',
                            collect([''=>'-- Pilih Pekerjaan / Instansi --'])->merge($listPekerjaan)->toArray(),
                            old('pekerjaan')
                        )
                        ->class('form-control select2 '.($errors->has('pekerjaan') ? 'is-invalid' : ''))
                        ->id('pekerjaan') !!}
                    @error('pekerjaan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Asal --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Asal Instansi / Sekolah / Universitas / Daerah', 'asal')->class('control-label fw-semibold') !!}
                    {!! html()->text('asal', old('asal'))
                        ->placeholder('Nama instansi, sekolah, atau universitas')
                        ->class('form-control '.($errors->has('asal') ? 'is-invalid' : ''))
                        ->id('asal') !!}
                    @error('asal')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Alamat --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Alamat Lengkap', 'alamat')->class('control-label fw-semibold') !!}
                    {!! html()->textarea('alamat', old('alamat'))
                        ->placeholder('Alamat lengkap')
                        ->class('form-control '.($errors->has('alamat') ? 'is-invalid' : ''))
                        ->id('alamat')
                        ->rows(3) !!}
                    @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Keperluan --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Keperluan Kunjungan <span class="text-danger">*</span>', 'keperluan')->class('control-label fw-semibold') !!}
                    {!! html()->select(
                            'keperluan',
                            collect([''=>'-- Pilih Keperluan --'])->merge($listKeperluan)->toArray(),
                            old('keperluan')
                        )
                        ->class('form-control select2 '.($errors->has('keperluan') ? 'is-invalid' : ''))
                        ->id('keperluan')
                        ->required() !!}
                    @error('keperluan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pesan --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Pesan / Kesan & Saran', 'pesan')->class('control-label fw-semibold') !!}
                    {!! html()->textarea('pesan', old('pesan'))
                        ->placeholder('Tuliskan pesan, kesan, atau saran...')
                        ->class('form-control '.($errors->has('pesan') ? 'is-invalid' : ''))
                        ->id('pesan')
                        ->rows(4)
                        ->maxlength(1000) !!}
                    <div class="text-end mt-1">
                        <small class="text-muted">
                            <span id="pesanCount">0</span> / 1000
                        </small>
                    </div>
                    @error('pesan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Upload Foto Tamu --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Upload Foto Tamu', 'foto')->class('control-label fw-semibold') !!}
                    {!! html()->file('foto')
                        ->class('form-control '.($errors->has('foto') ? 'is-invalid' : ''))
                        ->id('foto')
                        ->accept('image/jpeg,image/png') !!}
                    <small class="text-muted">Format: JPG / PNG. Maks 2MB.</small>
                    @error('foto')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Upload Dokumen Tamu --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Upload Dokumen Tamu', 'dokumen')->class('control-label fw-semibold') !!}
                    {!! html()->file('dokumen')
                        ->class('form-control '.($errors->has('dokumen') ? 'is-invalid' : ''))
                        ->id('dokumen')
                        ->accept('image/jpeg,image/png,application/pdf,.doc,.docx') !!}
                    <small class="text-muted">Format: JPG / PNG / PDF / DOC / DOCX. Maks 5MB.</small>
                    @error('dokumen')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

<style>
    .select2-container { z-index: 9999 !important; width: 100% !important; }
    .modal-lg { max-width: 1000px !important; }
    .form-group { margin-bottom: 0; }
    .form-control, .form-select { border-radius: 8px; }
    .invalid-feedback { display: block; }
</style>

<script>
    $('.select2').select2({ width: '100%' });

    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    // Counter pesan
    (function () {
        const pesanInput = document.getElementById('pesan');
        const pesanCount = document.getElementById('pesanCount');

        if (!pesanInput || !pesanCount) return;

        function updateCounter() {
            const total = pesanInput.value.length;
            pesanCount.innerText = total;
            pesanCount.classList.toggle('text-danger', total > 900);
        }

        pesanInput.addEventListener('input', updateCounter);
        updateCounter();
    })();
</script>