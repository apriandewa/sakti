{!! html()->modelForm($data, 'PUT', route($page->url.'.update', $data->id))
    ->id('form-create-'.$page->code)
    ->acceptsFiles()
    ->class('form form-horizontal')
    ->open() !!}

<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">

            {{-- Nama --}}
            <div class="col-md-7">
                <div class="form-group mb-3">
                    {!! html()->label('Nama Lengkap <span class="text-danger">*</span>', 'nama')->class('control-label fw-semibold') !!}
                    {!! html()->text('nama', old('nama', $data->nama))
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
                                   {{ old('jenis_kelamin', $data->jenis_kelamin) == 'Laki-laki' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jk_laki_laki">👨 Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin"
                                   id="jk_perempuan" value="Perempuan"
                                   {{ old('jenis_kelamin', $data->jenis_kelamin) == 'Perempuan' ? 'checked' : '' }}>
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
                    {!! html()->email('email', old('email', $data->email))
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
                    {!! html()->text('no_hp', old('no_hp', $data->no_hp))
                        ->placeholder('08xx-xxxx-xxxx')
                        ->class('form-control '.($errors->has('no_hp') ? 'is-invalid' : ''))
                        ->id('no_hp') !!}
                    @error('no_hp')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pekerjaan (dari tabel kategori) --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Pekerjaan / Instansi', 'pekerjaan')->class('control-label fw-semibold') !!}

                    {!! html()->select(
                            'pekerjaan',
                            collect([''=>'-- Pilih Pekerjaan / Instansi --'])->merge($listPekerjaan)->toArray(),
                            old('pekerjaan', $data->pekerjaan)
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
                    {!! html()->text('asal', old('asal', $data->asal))
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
                    {!! html()->textarea('alamat', old('alamat', $data->alamat))
                        ->placeholder('Alamat lengkap')
                        ->class('form-control '.($errors->has('alamat') ? 'is-invalid' : ''))
                        ->id('alamat')
                        ->rows(3) !!}
                    @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Keperluan (dari tabel kategori) --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Keperluan Kunjungan <span class="text-danger">*</span>', 'keperluan')->class('control-label fw-semibold') !!}

                    {!! html()->select(
                            'keperluan',
                            collect([''=>'-- Pilih Keperluan --'])->merge($listKeperluan)->toArray(),
                            old('keperluan', $data->keperluan)
                        )
                        ->class('form-control select2 '.($errors->has('keperluan') ? 'is-invalid' : ''))
                        ->id('keperluan')
                        ->required() !!}

                    @error('keperluan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Status <span class="text-danger">*</span>', 'status')->class('control-label fw-semibold') !!}
                    {!! html()->select('status', [
                            'TERKIRIM'  => 'TERKIRIM',
                            'DISETUJUI' => 'DISETUJUI',
                            'DITOLAK'   => 'DITOLAK',
                        ], old('status', $data->status))
                        ->class('form-control '.($errors->has('status') ? 'is-invalid' : ''))
                        ->id('status')
                        ->required() !!}
                    @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tanggal Kunjungan --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->label('Tanggal Kunjungan <span class="text-danger">*</span>', 'tanggal_kunjungan')->class('control-label fw-semibold') !!}
                    {!! html()->datetime('tanggal_kunjungan',
                            old('tanggal_kunjungan',
                                $data->tanggal_kunjungan
                                    ? \Carbon\Carbon::parse($data->tanggal_kunjungan)->format('Y-m-d\TH:i')
                                    : ''
                            ))
                        ->class('form-control '.($errors->has('tanggal_kunjungan') ? 'is-invalid' : ''))
                        ->id('tanggal_kunjungan')
                        ->required() !!}
                    @error('tanggal_kunjungan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Pesan --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Pesan / Kesan & Saran', 'pesan')->class('control-label fw-semibold') !!}
                    {!! html()->textarea('pesan', old('pesan', $data->pesan))
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
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Upload Foto Tamu', 'foto')->class('control-label fw-semibold') !!}

                    @if($fotoTamu)
                        <div class="mb-2" id="preview-foto">
                            <img src="{{ $fotoTamu->link_stream }}"
                                 alt="Foto Tamu"
                                 class="img-thumbnail"
                                 style="max-height: 160px; border-radius: 8px;">
                            <div class="mt-1">
                                <small class="text-muted">
                                    {{ $fotoTamu->name }} &bull; {{ $fotoTamu->size }}
                                </small>
                                {{-- ✅ PERBAIKAN: ganti href redirect → AJAX --}}
                                <button type="button"
                                        class="btn btn-xs btn-danger ms-2 btn-hapus-file"
                                        data-id="{{ $data->id }}"
                                        data-alias="foto_tamu"
                                        data-target="#preview-foto">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    @endif

                    {!! html()->file('foto')
                        ->class('form-control')
                        ->id('foto')
                        ->accept('image/jpeg,image/png') !!}
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                </div>
            </div>

            {{-- Upload Dokumen Tamu --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->label('Upload Dokumen', 'dokumen')->class('control-label fw-semibold') !!}

                    @if($dokumenTamu)
                        <div class="mb-2" id="preview-dokumen">
                            <small class="text-muted">
                                <i class="fa fa-file"></i>
                                {{ $dokumenTamu->name }} &bull; {{ $dokumenTamu->size }}
                            </small>
                            {{-- ✅ PERBAIKAN: ganti href redirect → AJAX --}}
                            <button type="button"
                                    class="btn btn-xs btn-danger ms-2 btn-hapus-file"
                                    data-id="{{ $data->id }}"
                                    data-alias="dokumen_tamu"
                                    data-target="#preview-dokumen">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </div>
                    @endif

                    {!! html()->file('dokumen')
                        ->class('form-control')
                        ->id('dokumen')
                        ->accept('image/jpeg,image/png,application/pdf,.doc,.docx') !!}
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah dokumen.</small>
                </div>
            </div>

        </div>
    </div>
</div>

{!! html()->hidden('table-id', 'datatable')->id('table-id') !!}
{!! html()->closeModelForm() !!}

<style>
    .select2-container { z-index: 9999 !important; width: 100% !important; }
    .modal-lg { max-width: 1000px !important; }
    .form-group { margin-bottom: 0; }
    .form-control, .form-select { border-radius: 8px; }
    .invalid-feedback { display: block; }
</style>

<script>
    $('.select2').select2({ width: '100%' });

    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    // Counter pesan
    const pesanInput = document.getElementById('pesan');
    const pesanCount = document.getElementById('pesanCount');
    if (pesanInput && pesanCount) {
        function updateCounter() {
            let total = pesanInput.value.length;
            pesanCount.innerText = total;
            pesanCount.classList.toggle('text-danger', total > 900);
        }
        pesanInput.addEventListener('input', updateCounter);
        updateCounter();
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ PERBAIKAN: Submit form edit via AJAX (sebelumnya tidak ada handler)
    |--------------------------------------------------------------------------
    */
    $(document).off('click.submitEdit').on('click.submitEdit', '.submit-data', function (e) {
        e.preventDefault();

        var $form    = $('#form-create-{{ $page->code }}');
        var formData = new FormData($form[0]);

        $.ajax({
            url        : $form.attr('action'),
            type       : 'POST',
            data       : formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('.submit-data').prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
            },
            success: function (res) {
                if (res.status) {
                    swal('Berhasil!', res.message, 'success', { button: 'OK' })
                        .then(function () {
                            // ✅ Reload DataTable tanpa pindah halaman
                            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
                                $('#datatable').DataTable().ajax.reload(null, true);
                            }
                            // Tutup modal
                            $('.modal').modal('hide');
                        });
                } else {
                    swal('Gagal!', res.message, 'error');
                    $('.submit-data').prop('disabled', false)
                        .html('<i class="fa fa-save"></i> Simpan Data');
                }
            },
            error: function (xhr) {
                var msg = 'Terjadi kesalahan.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    // Tampilkan pesan validasi Laravel jika ada
                    if (xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        msg = Object.values(errors).map(function(e){ return e[0]; }).join('<br>');
                    }
                }
                swal('Gagal!', msg, 'error');
                $('.submit-data').prop('disabled', false)
                    .html('<i class="fa fa-save"></i> Simpan Data');
            }
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ PERBAIKAN: Hapus file (foto/dokumen) via AJAX — bukan redirect
    |--------------------------------------------------------------------------
    */
    $(document).off('click.hapusFile').on('click.hapusFile', '.btn-hapus-file', function () {
        var $btn   = $(this);
        var id     = $btn.data('id');
        var alias  = $btn.data('alias');
        var target = $btn.data('target');
        var url    = '{{ url("") }}' + '/tamu/' + id + '/file';

        swal({
            title             : 'Hapus file?',
            text              : 'File akan dihapus secara permanen.',
            type              : 'warning',
            showCancelButton  : true,
            confirmButtonColor: '#dc3545',
            confirmButtonText : 'Ya, Hapus',
            cancelButtonText  : 'Batal',
            closeOnConfirm    : true,
        }, function () {
            $.ajax({
                url : url,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token : '{{ csrf_token() }}',
                    alias  : alias,
                },
                success: function (res) {
                    if (res.status) {
                        $(target).remove();
                        swal('Dihapus!', res.message, 'success');
                    } else {
                        swal('Gagal!', res.message, 'error');
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Gagal menghapus file.';
                    swal('Gagal!', msg, 'error');
                }
            });
        });
    });
</script>