{{ html()->form('PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->class('form form-horizontal')->data('confirm', 'Apakah Anda yakin ingin menyimpan verifikasi agenda rapat ini?')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12"><h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-calendar"></i> Detail Agenda Rapat</h5></div>
            <div class="col-md-8"><div class="form-group">{!! html()->span()->text("Nama Agenda")->class("control-label") !!}{!! html()->p($data->nama)->class("form-control") !!}</div></div>
            <div class="col-md-4"><div class="form-group">{!! html()->span()->text("Status")->class("control-label") !!}<div class="form-control">{!! $data->badge_status !!}</div></div></div>
            <div class="col-md-3"><div class="form-group">{!! html()->span()->text("Tanggal")->class("control-label") !!}{!! html()->p($data->tanggal ? $data->tanggal->format('d/m/Y') : '-')->class("form-control") !!}</div></div>
            <div class="col-md-3"><div class="form-group">{!! html()->span()->text("Jam")->class("control-label") !!}{!! html()->p(substr($data->jam_mulai,0,5).' - '.substr($data->jam_selesai,0,5))->class("form-control") !!}</div></div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->span()->text("Tempat")->class("control-label") !!}
                    @if(($data->tipe_rapat ?? 'offline') === 'online')
                        <div class="form-control" style="height:auto; min-height:38px;">
                            <strong>Online (Zoom)</strong><br>
                            ID: {{ $data->zoom_meeting_id }}<br>
                            Passcode: {{ $data->zoom_password }}
                        </div>
                    @else
                        {!! html()->p($data->tempat)->class("form-control") !!}
                    @endif
                </div>
            </div>
            <div class="col-md-3"><div class="form-group">{!! html()->span()->text("Pembuat")->class("control-label") !!}{!! html()->p($data->user->name ?? '-')->class("form-control") !!}</div></div>
            <div class="col-md-12"><div class="form-group">{!! html()->span()->text("Acara")->class("control-label") !!}{!! html()->p($data->acara)->class("form-control") !!}</div></div>
            @if($data->deskripsi)
            <div class="col-md-12"><div class="form-group">{!! html()->span()->text("Deskripsi")->class("control-label") !!}<div class="form-control" style="height:auto;">{!! $data->deskripsi !!}</div></div></div>
            @endif

            {{-- Pegawai Penanda Tangan --}}
            @if($data->pegawai)
            <div class="col-md-12 mt-2">
                <div class="alert alert-light border" style="font-size:13px;">
                    <i class="fa fa-user-circle"></i> <strong>Penanda Tangan:</strong> {{ $data->pegawai->nama }}
                    @if($data->pegawai->jabatanNama) — {{ $data->pegawai->jabatanNama->nama }} @endif
                    @if($data->pegawai->nip) <br><small class="text-muted">NIP. {{ $data->pegawai->nip }}</small> @endif
                </div>
            </div>
            @endif

            {{-- Dasar Surat --}}
            @if($data->dasar_dari)
            <div class="col-md-12 mt-3"><h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-paperclip"></i> Dasar Kegiatan</h5></div>
            <div class="col-md-3"><strong>Dari:</strong><br>{{ $data->dasar_dari }}</div>
            <div class="col-md-3"><strong>No:</strong><br>{{ $data->dasar_no }}</div>
            <div class="col-md-3"><strong>Tgl:</strong><br>{{ $data->dasar_tgl ? $data->dasar_tgl->format('d/m/Y') : '-' }}</div>
            <div class="col-md-3"><strong>Hal:</strong><br>{{ $data->dasar_hal }}</div>
            @if($dasar_surat->count() > 0)
            <div class="col-md-12 mt-2">
                @foreach($dasar_surat as $file)
                <div class="d-flex align-items-center gap-2 mb-1 p-2 rounded" style="background:#f8f9fa; border:1px solid #e9ecef;">
                    <i class="fa fa-file-o"></i><span style="flex:1;">{{ $file->name }}</span>
                    <a href="{{ url($file->link_stream) }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-eye"></i></a>
                    <a href="{{ url($file->link_download) }}" class="btn btn-xs btn-success" download><i class="fa fa-download"></i></a>
                </div>
                @endforeach
            </div>
            @endif
            @endif

            {{-- Histori Verifikasi --}}
            <div class="col-md-12 mt-3"><h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-history"></i> Riwayat Verifikasi</h5></div>
            <div class="col-md-12"><x-histori-verifikasi :histori="$histori_verifikasi" /></div>

            {{-- Form Verifikasi --}}
            @if($data->status === 'PENGAJUAN')
            <div class="col-md-12 mt-3"><h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-check-circle"></i> Form Verifikasi</h5></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Keputusan <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="">-- Pilih Keputusan --</option>
                        <option value="DITERIMA">✅ TERIMA</option>
                        <option value="REVISI">🔄 REVISI</option>
                        <option value="DITOLAK">❌ TOLAK</option>
                    </select>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label class="control-label">Catatan <span class="text-danger">*</span></label>
                    <textarea name="catatan" id="catatan" class="form-control" rows="3" required placeholder="Berikan catatan verifikasi..."></textarea>
                </div>
            </div>

            {{-- Passphrase BSrE — muncul hanya jika keputusan = DITERIMA dan jenis tte elektronik --}}
            @if(($data->jenis_tanda_tangan ?? 'manual') === 'elektronik')
            <div class="col-md-12" id="tte-passphrase-section" style="display:none;">
                <div class="p-3 rounded" style="background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border: 1px solid #a7f3d0;">
                    <h6 style="color:#065f46; margin-bottom:10px;">
                        <i class="fa fa-lock"></i> Tanda Tangan Elektronik (BSrE)
                    </h6>
                    <p style="font-size:12px; color:#047857; margin-bottom:10px;">
                        Dengan menerima agenda rapat ini, surat undangan akan dibuat dan ditandatangani secara elektronik melalui BSrE.
                        Masukkan passphrase sertifikat elektronik Anda untuk menandatangani.
                    </p>
                    @if($data->pegawai && $data->pegawai->user_id === auth()->id() && $data->pegawai->nik)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-weight:600; font-size:13px;">Passphrase BSrE <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" name="passphrase" id="input-passphrase-verif"
                                           class="form-control" autocomplete="off"
                                           placeholder="Masukkan passphrase sertifikat elektronik">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-passphrase-verif">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted"><i class="fa fa-shield"></i> Passphrase tidak akan disimpan di sistem.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="font-size:13px;">Penanda Tangan</label>
                                <div class="form-control" style="background:#f8f9fa;">
                                    <strong>{{ $data->pegawai->nama }}</strong>
                                    @if($data->pegawai->jabatanNama) — {{ $data->pegawai->jabatanNama->nama }} @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning" style="font-size:12px;">
                        <i class="fa fa-exclamation-triangle"></i>
                        @if(!$data->pegawai)
                            Penanda tangan belum ditentukan pada agenda rapat ini. TTE tidak dapat dilakukan.
                        @elseif($data->pegawai->user_id !== auth()->id())
                            Akun Anda tidak terhubung dengan penanda tangan yang ditunjuk (<strong>{{ $data->pegawai->nama }}</strong>). TTE tidak dapat dilakukan melalui akun ini.
                        @elseif(!$data->pegawai->nik)
                            NIK penanda tangan belum diisi. Silakan lengkapi data NIK pada modul Pegawai.
                        @endif
                        <br><small>Surat undangan tetap akan dibuat tanpa tanda tangan elektronik.</small>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(($data->jenis_tanda_tangan ?? 'manual') === 'manual')
            <div class="col-md-12" id="manual-info-section" style="display:none;">
                <div class="p-3 rounded" style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe;">
                    <h6 style="color:#1e3a8a; margin-bottom:5px;">
                        <i class="fa fa-info-circle"></i> Tanda Tangan Manual
                    </h6>
                    <p style="font-size:12px; color:#1e40af; margin-bottom:0;">
                        Agenda rapat ini menggunakan tanda tangan manual. Setelah verifikasi disimpan dengan status <strong>TERIMA</strong>, file PDF surat undangan dapat langsung didownload.
                    </p>
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->form()->close() !!}
<style>.modal-lg { max-width: 1000px !important; }</style>
<script>
    $('.modal-title').html('<i class="fa fa-check-circle"></i> Verifikasi Agenda Rapat');
    @if($data->status === 'PENGAJUAN')
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Verifikasi').show();
    @else
    $('.submit-data').hide();
    @endif

    // Toggle passphrase section based on status selection
    $('#status').on('change', function() {
        var status = $(this).val();
        var jenisTtd = '{{ $data->jenis_tanda_tangan ?? "manual" }}';
        
        if (status === 'DITERIMA') {
            if (jenisTtd === 'elektronik') {
                $('#tte-passphrase-section').slideDown(300);
                @if($data->pegawai && $data->pegawai->user_id === auth()->id() && $data->pegawai->nik)
                $('#input-passphrase-verif').prop('required', true);
                @endif
                $('#manual-info-section').hide();
            } else {
                $('#tte-passphrase-section').hide();
                $('#input-passphrase-verif').prop('required', false).val('');
                $('#manual-info-section').slideDown(300);
            }
        } else {
            $('#tte-passphrase-section').slideUp(300);
            $('#input-passphrase-verif').prop('required', false).val('');
            $('#manual-info-section').slideUp(300);
        }
    });

    // Toggle passphrase visibility
    $('#toggle-passphrase-verif').on('click', function() {
        var inp = $('#input-passphrase-verif');
        var icon = $(this).find('i');
        if (inp.attr('type') === 'password') {
            inp.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            inp.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
</script>
