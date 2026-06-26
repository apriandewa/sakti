{{ html()->form('PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form-horizontal')->open() }}

<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">

            {{-- ===== DATA AGENDA RAPAT ===== --}}
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-calendar"></i> Edit Agenda Rapat</h5>
            </div>

            @if($data->status === 'REVISI')
                @php
                    $latest_verifikasi = $data->verifikasi->last();
                @endphp
                @if($latest_verifikasi && $latest_verifikasi->catatan)
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> <strong>Catatan Revisi Verifikator:</strong><br>
                        {{ $latest_verifikasi->catatan }}
                    </div>
                </div>
                @endif
            @endif

            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('nama')->text('Nama Agenda Rapat') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('nama', $data->nama)->placeholder('Nama agenda rapat')->class('form-control')->id('nama') !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('tanggal')->text('Tanggal') !!}
                    <span class="text-danger">*</span>
                    {!! html()->date('tanggal', $data->tanggal ? $data->tanggal->format('Y-m-d') : null)->class('form-control')->id('tanggal') !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('jam_mulai')->text('Jam Mulai') !!}
                    <span class="text-danger">*</span>
                    <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="{{ substr($data->jam_mulai, 0, 5) }}" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('jam_selesai')->text('Jam Selesai') !!}
                    <span class="text-danger">*</span>
                    <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="{{ substr($data->jam_selesai, 0, 5) }}" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('tipe_rapat')->text('Jenis Rapat') !!}
                    <span class="text-danger">*</span>
                    <select name="tipe_rapat" id="tipe_rapat" class="form-control" required>
                        <option value="offline" {{ ($data->tipe_rapat ?? 'offline') == 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                        <option value="online" {{ ($data->tipe_rapat ?? 'offline') == 'online' ? 'selected' : '' }}>Online (Zoom Meeting)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6" id="tempat-offline-section" style="{{ ($data->tipe_rapat ?? 'offline') == 'online' ? 'display: none;' : '' }}">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('tempat')->text('Tempat / Lokasi Rapat') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('tempat', $data->tempat)->placeholder('Lokasi pelaksanaan rapat')->class('form-control')->id('tempat') !!}
                </div>
            </div>

            <div class="col-md-6" id="zoom-online-section" style="{{ ($data->tipe_rapat ?? 'offline') == 'online' ? '' : 'display: none;' }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->for('zoom_meeting_id')->text('Zoom Meeting ID') !!}
                            <span class="text-danger">*</span>
                            {!! html()->text('zoom_meeting_id', $data->zoom_meeting_id)->placeholder('Zoom Meeting ID')->class('form-control')->id('zoom_meeting_id') !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->for('zoom_password')->text('Zoom Passcode') !!}
                            <span class="text-danger">*</span>
                            {!! html()->text('zoom_password', $data->zoom_password)->placeholder('Zoom Passcode')->class('form-control')->id('zoom_password') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('acara')->text('Acara') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('acara', $data->acara)->placeholder('Acara / perihal rapat')->class('form-control')->id('acara') !!}
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label('Deskripsi', 'deskripsi')->class('control-label') !!}
                    {!! html()->textarea('deskripsi', $data->deskripsi)->class('form-control')->id('deskripsi')->placeholder('Deskripsi...')->attribute('rows', 3) !!}
                </div>
            </div>

            {{-- ===== DETAIL SURAT UNDANGAN ===== --}}
            <div class="col-md-12 mt-3">
                <h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-envelope"></i> Detail Surat Undangan</h5>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('surat_nomor')->text('Nomor Surat') !!}
                    {!! html()->text('surat_nomor', $data->surat_nomor)->placeholder('Nomor Surat')->class('form-control')->id('surat_nomor') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('surat_sifat')->text('Sifat Surat') !!}
                    {!! html()->text('surat_sifat', $data->surat_sifat)->placeholder('cth: Penting / Segera / Biasa')->class('form-control')->id('surat_sifat') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('surat_lampiran')->text('Lampiran') !!}
                    {!! html()->text('surat_lampiran', $data->surat_lampiran)->placeholder('cth: 1 (satu) berkas / -')->class('form-control')->id('surat_lampiran') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('surat_hal')->text('Hal') !!}
                    {!! html()->text('surat_hal', $data->surat_hal)->placeholder('Perihal Surat Undangan')->class('form-control')->id('surat_hal') !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('jenis_tujuan_surat')->text('Jenis Tujuan Surat') !!}
                    <span class="text-danger">*</span>
                    <select name="jenis_tujuan_surat" id="jenis_tujuan_surat" class="form-control" required>
                        <option value="tunggal" {{ ($data->jenis_tujuan_surat ?? 'tunggal') == 'tunggal' ? 'selected' : '' }}>Tunggal (Satu Penerima)</option>
                        <option value="lampiran" {{ ($data->jenis_tujuan_surat ?? 'tunggal') == 'lampiran' ? 'selected' : '' }}>Lampiran (Banyak Penerima / Daftar Terlampir)</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12" id="tujuan-tunggal-section" style="{{ ($data->jenis_tujuan_surat ?? 'tunggal') == 'lampiran' ? 'display: none;' : '' }}">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('surat_tujuan')->text('Tujuan Surat (Kepada Yth.)') !!}
                    <span class="text-danger">*</span>
                    {!! html()->textarea('surat_tujuan', $data->surat_tujuan)->class('form-control')->id('surat_tujuan')->placeholder('Kepada Yth. ...')->attribute('rows', 3) !!}
                </div>
            </div>

            <div class="col-md-12" id="tujuan-lampiran-section" style="{{ ($data->jenis_tujuan_surat ?? 'tunggal') == 'lampiran' ? '' : 'display: none;' }}">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('surat_tujuan_lampiran')->text('Daftar Penerima Undangan (Satu penerima per baris)') !!}
                    <span class="text-danger">*</span>
                    {!! html()->textarea('surat_tujuan_lampiran', $data->surat_tujuan_lampiran)->class('form-control')->id('surat_tujuan_lampiran')->placeholder("Contoh:\n1. Kepala Dinas Kominfotik\n2. Kepala Badan Kepegawaian\n3. Camat Rengat")->attribute('rows', 5) !!}
                    <small class="text-muted"><i class="fa fa-info-circle"></i> Tuliskan daftar penerima undangan di atas. Pada halaman pertama, tujuan surat akan otomatis ditulis <strong>Kepada Yth. Daftar terlampir</strong>, dan daftar penerima di atas akan ditampilkan di halaman kedua (lampiran).</small>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('pegawai_id')->text('Pegawai Penanda Tangan') !!}
                    <span class="text-danger">*</span>
                    <select name="pegawai_id" id="pegawai_id" class="form-control select2" required>
                        <option value="">Pilih Pegawai</option>
                        @foreach($pegawais as $pt)
                            <option value="{{ $pt->id }}" {{ $data->pegawai_id == $pt->id ? 'selected' : '' }}>{{ $pt->nama }} - {{ $pt->jabatan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('jenis_tanda_tangan')->text('Jenis Tanda Tangan') !!}
                    <span class="text-danger">*</span>
                    <select name="jenis_tanda_tangan" id="jenis_tanda_tangan" class="form-control select2" required>
                        <option value="manual" {{ $data->jenis_tanda_tangan == 'manual' ? 'selected' : '' }}>Manual</option>
                        <option value="elektronik" {{ $data->jenis_tanda_tangan == 'elektronik' ? 'selected' : '' }}>Elektronik (TTE)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('catatan')->text('Catatan / Keterangan Lain (Ditampilkan di Surat Undangan)') !!}
                    {!! html()->textarea('catatan', $data->catatan)->class('form-control')->id('catatan')->placeholder('Masukkan catatan tambahan untuk surat undangan... cth: Untuk informasi lebih lanjut dapat menghubungi Narahubung ...')->attribute('rows', 3) !!}
                </div>
            </div>

            {{-- ===== KONFLIK WARNING ===== --}}
            <div class="col-md-12" id="konflik-warning" style="display:none;">
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span id="konflik-message"></span>
                </div>
            </div>

            {{-- ===== DASAR KEGIATAN ===== --}}
            <div class="col-md-12 mt-3">
                <h5 class="text-muted border-bottom pb-1 mb-3">
                    <i class="fa fa-paperclip"></i> Dasar Kegiatan
                    <button type="button" class="btn btn-sm btn-outline-primary ml-2" id="btn-toggle-dasar">
                        <i class="fa fa-{{ $data->dasar_dari ? 'minus' : 'plus' }}-circle"></i>
                        {{ $data->dasar_dari ? 'Tutup' : 'Tambah' }} Dasar Kegiatan
                    </button>
                </h5>
            </div>

            <div id="dasar-kegiatan-form" style="{{ $data->dasar_dari ? '' : 'display:none;' }}" class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->for('dasar_dari')->text('Dasar Dari') !!}
                            {!! html()->text('dasar_dari', $data->dasar_dari)->placeholder('Instansi / Pejabat')->class('form-control')->id('dasar_dari') !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->for('dasar_no')->text('Nomor Surat') !!}
                            {!! html()->text('dasar_no', $data->dasar_no)->placeholder('Nomor surat')->class('form-control')->id('dasar_no') !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->for('dasar_tgl')->text('Tanggal Surat') !!}
                            {!! html()->date('dasar_tgl', $data->dasar_tgl ? $data->dasar_tgl->format('Y-m-d') : null)->class('form-control')->id('dasar_tgl') !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->for('dasar_hal')->text('Perihal') !!}
                            {!! html()->text('dasar_hal', $data->dasar_hal)->placeholder('Perihal surat')->class('form-control')->id('dasar_hal') !!}
                        </div>
                    </div>

                    {{-- Existing dasar surat files --}}
                    @if($dasar_surat->count() > 0)
                    <div class="col-md-12 mb-2">
                        <label class="control-label">Berkas Dasar Surat yang sudah ada:</label>
                        @foreach($dasar_surat as $file)
                        <div class="d-flex align-items-center gap-2 mb-1 p-2 rounded" style="background:#f8f9fa; border:1px solid #e9ecef;" id="dasar-file-{{ $file->id }}">
                            <i class="fa fa-file-o"></i>
                            <span style="flex:1; font-size:13px;">{{ $file->name }}</span>
                            <a href="{{ url($file->link_stream) }}" target="_blank" class="btn btn-xs btn-outline btn-info"><i class="fa fa-eye"></i> Preview</a>
                            <a href="javascript:void(0)" class="btn btn-xs btn-outline btn-danger delete-file"
                               data-url="{{ url($file->link_delete) }}" data-id="dasar-file-{{ $file->id }}"
                               data-title="Hapus Berkas" data-message="Hapus berkas {{ $file->name }}?">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Upload Dasar Surat Baru --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! html()->label()->class('control-label')->text('Tambah Berkas Dasar Surat') !!}
                            <small class="text-muted">(JPG, PNG, PDF | Maks 2MB per file)</small>
                            <div class="dropzone-area" id="dasar-dropzone">
                                <div class="dropzone-placeholder">
                                    <i class="fa fa-cloud-upload fa-3x text-muted"></i>
                                    <p class="text-muted mt-2">Drag & drop file disini atau <strong>klik untuk pilih file</strong></p>
                                </div>
                                <input type="file" name="dasar_surat[]" id="dasar_surat" multiple
                                       accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                            </div>
                            <div id="dasar-file-list" class="mt-2"></div>
                        </div>
                    </div>
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
    .dropzone-area {
        border: 2px dashed #ccc; border-radius: 8px; padding: 30px;
        text-align: center; cursor: pointer; transition: all 0.3s; background: #fafbfc;
    }
    .dropzone-area:hover, .dropzone-area.dragover { border-color: #007bff; background: #f0f7ff; }
    .dropzone-file-item {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 12px; background: #f8f9fa; border: 1px solid #e9ecef;
        border-radius: 6px; margin-bottom: 6px;
    }
    .dropzone-file-item .file-name { flex: 1; font-size: 13px; }
    .dropzone-file-item .file-size { font-size: 11px; color: #6c757d; }
    .dropzone-file-item .btn-remove { cursor: pointer; color: #dc3545; }
</style>

<script>
$(document).ready(function() {
    $('.select2').select2();
    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Perubahan');

    // Toggle Dasar Kegiatan
    $('#btn-toggle-dasar').on('click', function() {
        var $form = $('#dasar-kegiatan-form');
        if ($form.is(':visible')) {
            $form.slideUp();
            $(this).html('<i class="fa fa-plus-circle"></i> Tambah Dasar Kegiatan');
        } else {
            $form.slideDown();
            $(this).html('<i class="fa fa-minus-circle"></i> Tutup Dasar Kegiatan');
        }
    });

    // Dropzone (same as create)
    var dasarDropzone = document.getElementById('dasar-dropzone');
    var dasarInput = document.getElementById('dasar_surat');
    var dasarFileList = document.getElementById('dasar-file-list');
    var dasarFiles = new DataTransfer();

    dasarDropzone.addEventListener('click', function() { dasarInput.click(); });
    dasarDropzone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
    dasarDropzone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
    dasarDropzone.addEventListener('drop', function(e) {
        e.preventDefault(); this.classList.remove('dragover');
        handleDasarFiles(e.dataTransfer.files);
    });
    dasarInput.addEventListener('change', function() { handleDasarFiles(this.files); });

    function handleDasarFiles(files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var ext = file.name.split('.').pop().toLowerCase();
            if (!['jpg','jpeg','png','pdf'].includes(ext)) { swal('Error', 'Format ' + file.name + ' tidak diizinkan.', 'error'); continue; }
            if (file.size > 2 * 1024 * 1024) { swal('Error', file.name + ' melebihi 2MB.', 'error'); continue; }
            dasarFiles.items.add(file);
        }
        dasarInput.files = dasarFiles.files;
        renderDasarFileList();
    }

    function renderDasarFileList() {
        dasarFileList.innerHTML = '';
        for (var i = 0; i < dasarFiles.files.length; i++) {
            var f = dasarFiles.files[i];
            dasarFileList.innerHTML += '<div class="dropzone-file-item" data-index="' + i + '"><i class="fa fa-file-o"></i><span class="file-name">' + f.name + '</span><span class="file-size">' + (f.size / 1024).toFixed(1) + ' KB</span><span class="btn-remove" data-index="' + i + '"><i class="fa fa-times"></i></span></div>';
        }
    }

    $(document).on('click', '#dasar-file-list .btn-remove', function() {
        var idx = $(this).data('index');
        var newDt = new DataTransfer();
        for (var i = 0; i < dasarFiles.files.length; i++) { if (i !== idx) newDt.items.add(dasarFiles.files[i]); }
        dasarFiles = newDt; dasarInput.files = dasarFiles.files; renderDasarFileList();
    });

    // Toggle Tipe Rapat (Online / Offline)
    $('#tipe_rapat').on('change', function() {
        if ($(this).val() === 'online') {
            $('#tempat-offline-section').hide();
            $('#tempat').prop('required', false).val('');
            $('#zoom-online-section').show();
            $('#zoom_meeting_id').prop('required', true);
            $('#zoom_password').prop('required', true);
        } else {
            $('#tempat-offline-section').show();
            $('#tempat').prop('required', true);
            $('#zoom-online-section').hide();
            $('#zoom_meeting_id').prop('required', false).val('');
            $('#zoom_password').prop('required', false).val('');
        }
        checkKonflik();
    });

    // Toggle Jenis Tujuan Surat (Tunggal / Lampiran)
    $('#jenis_tujuan_surat').on('change', function() {
        if ($(this).val() === 'lampiran') {
            $('#tujuan-tunggal-section').hide();
            $('#surat_tujuan').prop('required', false).val('');
            $('#tujuan-lampiran-section').show();
            $('#surat_tujuan_lampiran').prop('required', true);
        } else {
            $('#tujuan-tunggal-section').show();
            $('#surat_tujuan').prop('required', true);
            $('#tujuan-lampiran-section').hide();
            $('#surat_tujuan_lampiran').prop('required', false).val('');
        }
    });

    // Check Konflik
    function checkKonflik() {
        var tipe_rapat = $('#tipe_rapat').val();
        var tanggal = $('#tanggal').val(), jam_mulai = $('#jam_mulai').val(), jam_selesai = $('#jam_selesai').val(), tempat = $('#tempat').val();
        
        if (tipe_rapat === 'online') {
            $('#konflik-warning').hide();
            return;
        }

        if (!tanggal || !jam_mulai || !jam_selesai || !tempat) { $('#konflik-warning').hide(); return; }
        $.post("{{ route('agenda-rapat.check-konflik') }}", {
            _token: '{{ csrf_token() }}', tanggal: tanggal, jam_mulai: jam_mulai, jam_selesai: jam_selesai, tempat: tempat, exclude_id: '{{ $data->id }}'
        }, function(res) {
            if (res.konflik) { $('#konflik-message').text(res.message); $('#konflik-warning').slideDown(); }
            else { $('#konflik-warning').slideUp(); }
        });
    }
    $('#tanggal, #jam_mulai, #jam_selesai, #tempat').on('change', function() { checkKonflik(); });

    // Trigger initial values
    // We don't overwrite current DB values with empty string on load, so we just set validation requirements:
    if ($('#tipe_rapat').val() === 'online') {
        $('#tempat').prop('required', false);
        $('#zoom_meeting_id').prop('required', true);
        $('#zoom_password').prop('required', true);
    } else {
        $('#tempat').prop('required', true);
        $('#zoom_meeting_id').prop('required', false);
        $('#zoom_password').prop('required', false);
    }

    if ($('#jenis_tujuan_surat').val() === 'lampiran') {
        $('#surat_tujuan').prop('required', false);
        $('#surat_tujuan_lampiran').prop('required', true);
    } else {
        $('#surat_tujuan').prop('required', true);
        $('#surat_tujuan_lampiran').prop('required', false);
    }
});
</script>
