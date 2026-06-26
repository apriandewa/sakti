@extends('backend.main.index')
@push('title', ($page->title ?? 'Agenda Rapat') . ' - Detail')
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="me-auto">
                    <h3 class="page-title"><i class="{!! $page->icon ?? 'fa fa-calendar' !!}"></i> Detail Agenda</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('agenda-rapat.index') }}">{!! $page->title ?? 'Agenda Rapat' !!}</a></li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if(in_array($data->status, ['DRAFT', 'REVISI']) && $user->level_id == 2)
                        <button type="button" class="btn btn-warning btn-sm" id="btn-kirim-agenda">
                            <i class="fa fa-paper-plane"></i> Kirim Pengajuan
                        </button>
                    @endif
                    <a href="{{ route('agenda-rapat.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="box shadow-md">
                <div class="box-body">
        <div class="row">
            {{-- ===== DATA AGENDA RAPAT ===== --}}
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-calendar"></i> Detail Agenda Rapat</h5>
            </div>

            <div class="col-md-8">
                <div class="form-group">
                    {!! html()->span()->text("Nama Agenda")->class("control-label") !!}
                    {!! html()->p($data->nama)->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->span()->text("Status")->class("control-label") !!}
                    <div class="form-control">{!! $data->badge_status !!}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->span()->text("Tanggal")->class("control-label") !!}
                    {!! html()->p($data->tanggal ? $data->tanggal->format('d/m/Y') : '-')->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->span()->text("Jam Mulai")->class("control-label") !!}
                    {!! html()->p(substr($data->jam_mulai, 0, 5))->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! html()->span()->text("Jam Selesai")->class("control-label") !!}
                    {!! html()->p(substr($data->jam_selesai, 0, 5))->class("form-control") !!}
                </div>
            </div>
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
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span()->text("Acara")->class("control-label") !!}
                    {!! html()->p($data->acara)->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span()->text("Dibuat Oleh")->class("control-label") !!}
                    {!! html()->p($data->user->name ?? '-')->class("form-control") !!}
                </div>
            </div>
            @if($data->deskripsi)
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->span()->text("Deskripsi")->class("control-label") !!}
                    <div class="form-control" style="height:auto; min-height:50px;">{!! $data->deskripsi !!}</div>
                </div>
            </div>
            @endif

            @if($data->catatan)
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->span()->text("Catatan Undangan")->class("control-label") !!}
                    <div class="form-control" style="height:auto; min-height:50px;">{!! nl2br(e($data->catatan)) !!}</div>
                </div>
            </div>
            @endif

            @if($data->status === 'REVISI')
                @php
                    $latest_verifikasi = $data->verifikasi->last();
                @endphp
                @if($latest_verifikasi && $latest_verifikasi->catatan)
                <div class="col-md-12">
                    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <strong>Catatan Revisi Verifikator:</strong> {{ $latest_verifikasi->catatan }}</div>
                </div>
                @endif
            @endif
        </div>

        {{-- ===== TAB PANEL ===== --}}
        <ul class="nav nav-tabs mt-4" role="tablist">
            @if($data->dasar_dari)
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-dasar"><i class="fa fa-paperclip"></i> Dasar Surat</a></li>
            @endif
            <li class="nav-item"><a class="nav-link {{ !$data->dasar_dari ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-undangan"><i class="fa fa-envelope"></i> Undangan</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-absensi"><i class="fa fa-users"></i> Daftar Hadir ({{ $data->peserta->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-materi"><i class="fa fa-file-text"></i> Materi ({{ $materi->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-dokumentasi"><i class="fa fa-camera"></i> Dokumentasi ({{ $dokumentasi->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-notulen"><i class="fa fa-pencil-square-o"></i> Notulen</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-verifikasi"><i class="fa fa-history"></i> Riwayat</a></li>
        </ul>

        <div class="tab-content p-3">
            {{-- TAB: DASAR SURAT --}}
            @if($data->dasar_dari)
            <div class="tab-pane fade show active" id="tab-dasar">
                <div class="row">
                    <div class="col-md-3"><strong>Dasar Dari:</strong><br>{{ $data->dasar_dari }}</div>
                    <div class="col-md-3"><strong>No. Surat:</strong><br>{{ $data->dasar_no }}</div>
                    <div class="col-md-3"><strong>Tanggal:</strong><br>{{ $data->dasar_tgl ? $data->dasar_tgl->format('d/m/Y') : '-' }}</div>
                    <div class="col-md-3"><strong>Perihal:</strong><br>{{ $data->dasar_hal }}</div>
                </div>
                @if($dasar_surat->count() > 0)
                <hr>
                <h6>Berkas Dasar Surat:</h6>
                @foreach($dasar_surat as $file)
                <div class="d-flex align-items-center gap-2 mb-1 p-2 rounded" style="background:#f8f9fa; border:1px solid #e9ecef;">
                    <i class="fa fa-file-o"></i>
                    <span style="flex:1; font-size:13px;">{{ $file->name }}</span>
                    <a href="{{ url($file->link_stream) }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Preview</a>
                    <a href="{{ url($file->link_download) }}" class="btn btn-xs btn-success" download><i class="fa fa-download"></i></a>
                </div>
                @endforeach
                @endif
            </div>
            @endif

            {{-- TAB: UNDANGAN --}}
            <div class="tab-pane fade {{ !$data->dasar_dari ? 'show active' : '' }}" id="tab-undangan">
                @if($data->status === 'DITERIMA')
                <div class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>Undangan Rapat Siap Didownload</h5>
                    @if($data->jenis_tanda_tangan === 'manual')
                    <a href="{{ route('agenda-rapat.export-undangan', $data->id) }}" class="btn btn-primary mt-2" target="_blank">
                        <i class="fa fa-download"></i> Download Undangan (PDF)
                    </a>
                    @endif

                    {{-- TTE BSrE: Status Undangan --}}
                    @if($tteUndangan && $tteUndangan->status === 'signed')
                    <div class="mt-3">
                        <div class="alert alert-success d-inline-block" style="font-size:13px;">
                            <i class="fa fa-check-circle"></i> <strong>Sudah Ditandatangani Elektronik (BSrE)</strong>
                            <br><small>{{ $tteUndangan->signed_at->format('d/m/Y H:i') }} oleh {{ $tteUndangan->pegawai->nama ?? '-' }}</small>
                        </div>
                        <br>
                        <a href="{{ route('agenda-rapat.download-signed', [$data->id, 'undangan']) }}" class="btn btn-success btn-sm mt-1" target="_blank">
                            <i class="fa fa-download"></i> Download Dokumen Bertanda Tangan
                        </a>
                    </div>
                    @endif

                    <hr>
                    <h6>Link Daftar Hadir Online</h6>
                    <div class="input-group" style="max-width:500px; margin:0 auto;">
                        <input type="text" class="form-control" value="{{ $data->absensi_url }}" id="absensi-link" readonly>
                        <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText($('#absensi-link').val()); swal('Tersalin!', 'Link absensi berhasil disalin.', 'success');">
                            <i class="fa fa-copy"></i> Salin
                        </button>
                    </div>
                    <div class="mt-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($data->absensi_url) }}" alt="QR Code Absensi" style="border:2px solid #ddd; border-radius:8px; padding:5px;">
                        <p class="text-muted mt-2"><small>Scan QR Code untuk mengisi daftar hadir</small></p>
                    </div>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                    <p>Undangan rapat hanya bisa didownload setelah status <strong>DITERIMA</strong>.</p>
                </div>
                @endif
            </div>

            {{-- TAB: DAFTAR HADIR --}}
            <div class="tab-pane fade" id="tab-absensi">
                @if($data->peserta->count() > 0)
                <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
                    @if($data->jenis_tanda_tangan === 'manual')
                    <a href="{{ route('agenda-rapat.export-daftar-hadir', $data->id) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> Export PDF</a>
                    @endif

                    {{-- TTE BSrE: Daftar Hadir --}}
                    @if($tteDaftarHadir && $tteDaftarHadir->status === 'signed')
                        <span class="badge bg-success" style="font-size:11px; padding:6px 12px;">
                            <i class="fa fa-check-circle"></i> Ditandatangani {{ $tteDaftarHadir->signed_at->format('d/m/Y H:i') }}
                        </span>
                        <a href="{{ route('agenda-rapat.download-signed', [$data->id, 'daftar_hadir']) }}" class="btn btn-sm btn-success" target="_blank">
                            <i class="fa fa-download"></i> Download TTE
                        </a>
                    @elseif($isSignatory && $data->status === 'DITERIMA')
                        <button class="btn btn-sm btn-warning btn-sign-dokumen" data-jenis="daftar_hadir" data-agenda="{{ $data->id }}">
                            <i class="fa fa-pencil-square"></i> Tanda Tangani (BSrE)
                        </button>
                    @endif
                </div>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Instansi</th>
                            <th>No HP</th>
                            <th>Waktu Hadir</th>
                            <th>TTD</th>
                            @if(in_array($user->level_id, [1, 2]))
                                <th class="text-center w-0">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data->peserta as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->nip ?? '-' }}</td>
                        <td>{{ $p->jabatan ?? '-' }}</td>
                        <td>{{ $p->instansi ?? '-' }}</td>
                        <td>{{ $p->no_hp ?? '-' }}</td>
                        <td>{{ $p->waktu_hadir ? $p->waktu_hadir->format('d/m/Y H:i') : '-' }}</td>
                        <td>@if($p->tanda_tangan)<img src="{{ $p->tanda_tangan }}" style="max-height:40px;">@else - @endif</td>
                        @if(in_array($user->level_id, [1, 2]))
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-xs btn-warning btn-action" 
                                            data-title="Edit Peserta" 
                                            data-action="edit" 
                                            data-url="{{ route('agenda-rapat.index') }}/peserta" 
                                            data-id="{{ $p->id }}" 
                                            data-modal-id="modal-peserta" 
                                            data-size="modal-md" 
                                            title="Edit Peserta">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-xs btn-danger btn-action" 
                                            data-title="Hapus Peserta" 
                                            data-action="delete" 
                                            data-url="{{ route('agenda-rapat.index') }}/peserta" 
                                            data-id="{{ $p->id }}" 
                                            data-modal-id="modal-peserta" 
                                            data-size="modal-md" 
                                            title="Hapus Peserta">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-muted text-center py-3"><i class="fa fa-info-circle"></i> Belum ada peserta yang mengisi daftar hadir.</p>
                @endif
            </div>

            {{-- TAB: NOTULEN --}}
            <div class="tab-pane fade" id="tab-notulen">
                @php
                    $isNotulis = $data->notulen && $pegawai_login && $data->notulen->notulis_id === $pegawai_login->id;
                    $isPimpinan = $data->notulen && $pegawai_login && $data->notulen->pimpinan_rapat_id === $pegawai_login->id;
                @endphp

                @if($data->notulen)
                <div class="mb-2 d-flex align-items-center gap-2 flex-wrap">
                    @if(in_array($data->notulen->status, ['DISETUJUI', 'SELESAI']))
                        @if($data->jenis_tanda_tangan === 'manual')
                        <a href="{{ route('agenda-rapat.export-notulen', $data->id) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> Export PDF (Manual)</a>
                        @endif
                        
                        {{-- TTE BSrE: Notulen Notulis --}}
                        @if($tteNotulenNotulis && $tteNotulenNotulis->status === 'signed')
                            <span class="badge bg-success" style="font-size:11px; padding:6px 12px;">
                                <i class="fa fa-check-circle"></i> Notulis Ditandatangani {{ $tteNotulenNotulis->signed_at->format('d/m/Y H:i') }}
                            </span>
                        @elseif($data->jenis_tanda_tangan === 'elektronik' && $isNotulis)
                            <button class="btn btn-sm btn-warning btn-sign-dokumen" data-jenis="notulen_notulis" data-agenda="{{ $data->id }}">
                                <i class="fa fa-pencil-square"></i> Tanda Tangani Notulis
                            </button>
                        @endif

                        {{-- TTE BSrE: Notulen Pimpinan --}}
                        @if($tteNotulenPimpinan && $tteNotulenPimpinan->status === 'signed')
                            <span class="badge bg-success" style="font-size:11px; padding:6px 12px;">
                                <i class="fa fa-check-circle"></i> Pimpinan Ditandatangani {{ $tteNotulenPimpinan->signed_at->format('d/m/Y H:i') }}
                            </span>
                        @elseif($data->jenis_tanda_tangan === 'elektronik' && $isPimpinan && $tteNotulenNotulis && $tteNotulenNotulis->status === 'signed')
                            <button class="btn btn-sm btn-warning btn-sign-dokumen" data-jenis="notulen_pimpinan" data-agenda="{{ $data->id }}">
                                <i class="fa fa-pencil-square"></i> Tanda Tangani Pimpinan
                            </button>
                        @elseif($data->jenis_tanda_tangan === 'elektronik' && $isPimpinan)
                            <span class="badge bg-secondary" style="font-size:11px; padding:6px 12px;"><i class="fa fa-clock-o"></i> Menunggu TTE Notulis</span>
                        @endif

                        {{-- Download Tombol (Tergantung siapa yang terakhir tanda tangan) --}}
                        @if($tteNotulenPimpinan && $tteNotulenPimpinan->status === 'signed')
                            <a href="{{ route('agenda-rapat.download-signed', [$data->id, 'notulen_pimpinan']) }}" class="btn btn-sm btn-success" target="_blank">
                                <i class="fa fa-download"></i> Download Notulen TTE (Final)
                            </a>
                        @elseif($tteNotulenNotulis && $tteNotulenNotulis->status === 'signed')
                            <a href="{{ route('agenda-rapat.download-signed', [$data->id, 'notulen_notulis']) }}" class="btn btn-sm btn-success" target="_blank">
                                <i class="fa fa-download"></i> Download Notulen TTE (Notulis Saja)
                            </a>
                        @endif
                    @endif
                </div>

                <div class="alert alert-info">
                    <strong>Status Notulen:</strong> {{ str_replace('_', ' ', $data->notulen->status) }}
                    @if($data->notulen->status === 'REVISI' && $data->notulen->catatan_revisi)
                        <br><strong>Catatan Revisi:</strong> {{ $data->notulen->catatan_revisi }}
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-md-6"><strong>Pimpinan Rapat:</strong> {{ $data->notulen->pimpinan_rapat }}</div>
                    <div class="col-md-6"><strong>Notulis:</strong> {{ $data->notulen->notulis }}</div>
                </div>
                
                <h6>Isi Notulen:</h6>
                <div class="p-3" style="background:#f8f9fa; border-radius:8px;">{!! $data->notulen->isi_notulen !!}</div>
                @if($data->notulen->hasil_rapat)
                <h6 class="mt-3">Hasil / Kesimpulan Rapat:</h6>
                <div class="p-3" style="background:#f0faf0; border-radius:8px;">{!! $data->notulen->hasil_rapat !!}</div>
                @endif
                
                @if(in_array($data->notulen->status, ['DRAFT', 'REVISI']))
                    <div class="mt-3">
                        @if(in_array($user->level_id, [1, 2]))
                            <button type="button" class="btn btn-primary btn-sm" id="btn-edit-notulen"><i class="fa fa-edit"></i> Edit Notulen</button>
                            <button type="button" class="btn btn-warning btn-sm" id="btn-kirim-notulen"><i class="fa fa-paper-plane"></i> Kirim ke Pimpinan Rapat</button>
                        @endif
                    </div>
                @elseif($data->notulen->status === 'MENUNGGU_PERSETUJUAN')
                    @if($isPimpinan)
                        <div class="mt-3">
                            <button type="button" class="btn btn-success btn-sm" id="btn-setuju-notulen"><i class="fa fa-check"></i> Setujui Notulen</button>
                            <button type="button" class="btn btn-danger btn-sm" id="btn-revisi-notulen"><i class="fa fa-times"></i> Minta Revisi</button>
                        </div>
                    @endif
                @endif
                @endif

                @if(in_array($user->level_id, [1, 2]))
                <div id="form-notulen-container" style="display: {{ (!$data->notulen || in_array($data->notulen->status ?? '', ['DRAFT', 'REVISI'])) ? 'block' : 'none' }};">
                    <hr>
                    <h6><i class="fa fa-pencil"></i> {{ $data->notulen ? 'Edit' : 'Input' }} Notulen Rapat</h6>
                    <form id="form-notulen">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pimpinan Rapat <span class="text-danger">*</span></label>
                                    <select name="pimpinan_rapat_id" class="form-control select2" required>
                                        <option value="">Pilih Pimpinan Rapat</option>
                                        @foreach($pegawais as $pt)
                                            <option value="{{ $pt->id }}" {{ (isset($data->notulen) && $data->notulen->pimpinan_rapat_id == $pt->id) ? 'selected' : '' }}>
                                                {{ $pt->nama }} - {{ $pt->jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Notulis <span class="text-danger">*</span></label>
                                    <select name="notulis_id" class="form-control select2" required>
                                        <option value="">Pilih Notulis</option>
                                        @foreach($pegawais as $pt)
                                            <option value="{{ $pt->id }}" {{ (isset($data->notulen) && $data->notulen->notulis_id == $pt->id) ? 'selected' : '' }}>
                                                {{ $pt->nama }} - {{ $pt->jabatan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12"><div class="form-group"><label>Isi Notulen <span class="text-danger">*</span></label><textarea name="isi_notulen" class="form-control" rows="5" required>{{ $data->notulen->isi_notulen ?? '' }}</textarea></div></div>
                            <div class="col-md-12"><div class="form-group"><label>Hasil / Kesimpulan Rapat</label><textarea name="hasil_rapat" class="form-control" rows="3">{{ $data->notulen->hasil_rapat ?? '' }}</textarea></div></div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Simpan Draft Notulen</button>
                                @if($data->notulen)
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="$('#form-notulen-container').hide();"><i class="fa fa-times"></i> Batal</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>

            {{-- TAB: DOKUMENTASI --}}
            <div class="tab-pane fade" id="tab-dokumentasi">
                @if($dokumentasi->count() > 0)
                <div class="row">
                    @foreach($dokumentasi as $foto)
                    <div class="col-md-3 mb-3" id="dok-{{ $foto->id }}">
                        <div class="card">
                            <a href="{{ url($foto->link_stream) }}" target="_blank">
                                <img src="{{ url($foto->link_stream) }}" class="card-img-top" style="height:150px; object-fit:cover;">
                            </a>
                            <div class="card-body p-2 text-center">
                                <small>{{ $foto->name }}</small><br>
                                <a href="javascript:void(0)" class="btn btn-xs btn-danger mt-1 delete-file" data-url="{{ url($foto->link_delete) }}" data-id="dok-{{ $foto->id }}" data-title="Hapus Foto" data-message="Hapus foto ini?"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($user->level_id == 2)
                <hr>
                <h6><i class="fa fa-upload"></i> Upload Dokumentasi</h6>
                <form id="form-dokumentasi" enctype="multipart/form-data">
                    @csrf
                    <div class="dropzone-area" id="dok-dropzone">
                        <i class="fa fa-camera fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Drag & drop foto atau <strong>klik untuk pilih</strong></p>
                        <small class="text-muted">Format: JPG, PNG | Maks: 2MB (otomatis kompres jika lebih)</small>
                        <input type="file" name="dokumentasi[]" id="dok-input" multiple accept=".jpg,.jpeg,.png" style="display:none;">
                    </div>
                    <div id="dok-file-list" class="mt-2"></div>
                    <button type="submit" class="btn btn-success btn-sm mt-2"><i class="fa fa-upload"></i> Upload Dokumentasi</button>
                </form>
                @endif
            </div>

            {{-- TAB: MATERI --}}
            <div class="tab-pane fade" id="tab-materi">
                @if($materi->count() > 0)
                @foreach($materi as $mat)
                <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded" style="background:#f8f9fa; border:1px solid #e9ecef;" id="mat-{{ $mat->id }}">
                    <i class="fa fa-file-text-o"></i>
                    <span style="flex:1;">{{ $mat->name }}</span>
                    <a href="{{ url($mat->link_stream) }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Preview</a>
                    <a href="{{ url($mat->link_download) }}" class="btn btn-xs btn-success" download><i class="fa fa-download"></i></a>
                    <a href="javascript:void(0)" class="btn btn-xs btn-danger delete-file" data-url="{{ url($mat->link_delete) }}" data-id="mat-{{ $mat->id }}" data-title="Hapus Materi" data-message="Hapus materi ini?"><i class="fa fa-trash"></i></a>
                </div>
                @endforeach
                @endif

                @if($user->level_id == 2)
                <hr>
                <h6><i class="fa fa-upload"></i> Upload Bahan / Materi Rapat</h6>
                <form id="form-materi" enctype="multipart/form-data">
                    @csrf
                    <div class="dropzone-area" id="mat-dropzone">
                        <i class="fa fa-file-text fa-3x text-muted"></i>
                        <p class="text-muted mt-2">Drag & drop dokumen atau <strong>klik untuk pilih</strong></p>
                        <small class="text-muted">Format: Word, PDF, PPT | Maks: 2MB</small>
                        <input type="file" name="materi[]" id="mat-input" multiple accept=".doc,.docx,.pdf,.ppt,.pptx" style="display:none;">
                    </div>
                    <div id="mat-file-list" class="mt-2"></div>
                    <button type="submit" class="btn btn-success btn-sm mt-2"><i class="fa fa-upload"></i> Upload Materi</button>
                </form>
                @endif
            </div>

            {{-- TAB: VERIFIKASI --}}
            <div class="tab-pane fade" id="tab-verifikasi">
                <x-histori-verifikasi :histori="$histori_verifikasi" />
            </div>
        </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

{{-- Modal Passphrase BSrE --}}
<div class="modal fade" id="modal-passphrase" tabindex="-1" style="z-index:99999;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a1a2e,#16213e); color:#fff; border:none;">
                <h5 class="modal-title" style="font-size:15px;">
                    <i class="fa fa-lock"></i> Tanda Tangan Elektronik
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-sign-dokumen">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size:12px; border-radius:8px;">
                        <i class="fa fa-info-circle"></i>
                        Masukkan <strong>passphrase</strong> sertifikat elektronik Anda yang terdaftar di <strong>BSrE (BSSN)</strong>.
                    </div>
                    <input type="hidden" name="jenis_dokumen" id="sign-jenis">
                    <input type="hidden" name="agenda_id" id="sign-agenda-id">
                    <div class="form-group">
                        <label style="font-weight:600; font-size:13px;">Passphrase <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="passphrase" id="input-passphrase"
                                   class="form-control" required
                                   autocomplete="off" placeholder="Masukkan passphrase">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-passphrase">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Passphrase tidak akan disimpan di sistem.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#065f46,#059669); color:#fff;">
                        <i class="fa fa-pencil-square"></i> Tanda Tangani
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
<style>
.dropzone-area { border:2px dashed #ccc; border-radius:8px; padding:30px; text-align:center; cursor:pointer; transition:all .3s; background:#fafbfc; }
.dropzone-area:hover,.dropzone-area.dragover { border-color:#007bff; background:#f0f7ff; }
.dropzone-file-item { display:flex; align-items:center; gap:10px; padding:8px 12px; background:#f8f9fa; border:1px solid #e9ecef; border-radius:6px; margin-bottom:6px; }
.dropzone-file-item .file-name { flex:1; font-size:13px; }
.dropzone-file-item .btn-remove { cursor:pointer; color:#dc3545; }
.nav-tabs .nav-link { font-size:13px; }
.select2-container { z-index: 9999 !important; width: 100% !important; }
.btn-sign-dokumen { transition: all .3s; }
.btn-sign-dokumen:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>
@endpush

@push('js')
<script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
<script src="{{ url('js/jquery-crud.js') }}"></script>

<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
    }

    // Restore active tab from sessionStorage if available
    var activeTab = sessionStorage.getItem('activeTab_agenda_' + "{{ $data->id }}");
    if (activeTab) {
        $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
    }

    // Save active tab to sessionStorage when switched
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", $(e.target).attr('href'));
    });

    // KIRIM button
    @if(in_array($data->status, ['DRAFT', 'REVISI']) && $user->level_id == 2)
    $('#btn-kirim-agenda').on('click', function(e) {
        e.preventDefault();
        swal({ title:'Konfirmasi', text:'Kirim agenda rapat ini untuk verifikasi?', type:'warning', showCancelButton:true, confirmButtonText:'Ya, Kirim!', cancelButtonText:'Batal' }, function(c) {
            if(c) {
                $.post("{{ route('agenda-rapat.kirim', $data->id) }}", { _token:'{{ csrf_token() }}' }, function(res) {
                    if(res.status) { 
                        swal('Berhasil!', res.message, 'success'); 
                        setTimeout(function(){ 
                            window.location.href = "{{ route('agenda-rapat.index') }}"; 
                        }, 1500); 
                    }
                    else { swal('Gagal!', res.message, 'error'); }
                });
            }
        });
    });
    @endif

    // NOTULEN handlers
    $('#btn-edit-notulen').on('click', function() {
        $('#form-notulen-container').slideDown();
    });

    $('#form-notulen').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('agenda-rapat.store-notulen', $data->id) }}",
            type: 'POST', data: formData,
            success: function(res) {
                if(res.status) {
                    swal('Berhasil!', res.message, 'success');
                    sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-notulen');
                    setTimeout(function(){ location.reload(); }, 1500);
                } else {
                    swal('Gagal!', res.message, 'error');
                }
            },
            error: function(xhr) { swal('Error!', 'Terjadi kesalahan.', 'error'); }
        });
    });

    $('#btn-kirim-notulen').on('click', function() {
        swal({ title:'Konfirmasi', text:'Kirim notulen ini ke Pimpinan Rapat untuk disetujui?', type:'info', showCancelButton:true, confirmButtonText:'Ya, Kirim', cancelButtonText:'Batal' }, function(c) {
            if(c) {
                $.post("{{ route('agenda-rapat.kirim-notulen', $data->id) }}", { _token:'{{ csrf_token() }}' }, function(res) {
                    if(res.status) { 
                        swal('Berhasil!', res.message, 'success'); 
                        sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-notulen');
                        setTimeout(function(){ location.reload(); }, 1500); 
                    }
                    else { swal('Gagal!', res.message, 'error'); }
                });
            }
        });
    });

    $('#btn-setuju-notulen').on('click', function() {
        swal({ title:'Konfirmasi', text:'Anda yakin menyetujui notulen ini? Setelah disetujui, notulen tidak bisa diedit dan siap ditandatangani.', type:'warning', showCancelButton:true, confirmButtonText:'Ya, Setujui', cancelButtonText:'Batal' }, function(c) {
            if(c) {
                $.post("{{ route('agenda-rapat.setuju-notulen', $data->id) }}", { _token:'{{ csrf_token() }}' }, function(res) {
                    if(res.status) { 
                        swal('Berhasil!', res.message, 'success'); 
                        sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-notulen');
                        setTimeout(function(){ location.reload(); }, 1500); 
                    }
                    else { swal('Gagal!', res.message, 'error'); }
                });
            }
        });
    });

    // Minta Revisi Notulen
    $('#btn-revisi-notulen').on('click', function() {
        swal({
            title: "Minta Revisi",
            text: "Masukkan catatan revisi:",
            type: "input",
            showCancelButton: true,
            closeOnConfirm: false,
            animation: "slide-from-top",
            inputPlaceholder: "Catatan revisi..."
        }, function(inputValue){
            if (inputValue === false) return false;
            if (inputValue === "") { swal.showInputError("Catatan revisi harus diisi!"); return false }
            
            $.post("{{ route('agenda-rapat.revisi-notulen', $data->id) }}", { _token:'{{ csrf_token() }}', catatan: inputValue }, function(res) {
                if(res.status) { 
                    swal('Berhasil!', res.message, 'success'); 
                    sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-notulen');
                    setTimeout(function(){ location.reload(); }, 1500); 
                }
                else { swal('Gagal!', res.message, 'error'); }
            });
        });
    });

    // Generic dropzone handler
    function initDropzone(dzId, inputId, listId, allowedExts, maxSize) {
        var dz = document.getElementById(dzId), inp = document.getElementById(inputId), list = document.getElementById(listId);
        if (!dz || !inp || !list) return;
        var dt = new DataTransfer();
        dz.addEventListener('click', function() { inp.click(); });
        dz.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
        dz.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
        dz.addEventListener('drop', function(e) { e.preventDefault(); this.classList.remove('dragover'); addFiles(e.dataTransfer.files); });
        inp.addEventListener('change', function() { addFiles(this.files); });

        function addFiles(files) {
            for(var i=0; i<files.length; i++) {
                var ext = files[i].name.split('.').pop().toLowerCase();
                if(allowedExts.indexOf(ext) === -1) { swal('Error','Format '+files[i].name+' tidak diizinkan.','error'); continue; }
                if(files[i].size > maxSize) { swal('Error',files[i].name+' melebihi batas ukuran.','error'); continue; }
                dt.items.add(files[i]);
            }
            inp.files = dt.files; render();
        }
        function render() {
            list.innerHTML = '';
            for(var i=0; i<dt.files.length; i++) {
                list.innerHTML += '<div class="dropzone-file-item"><i class="fa fa-file-o"></i><span class="file-name">'+dt.files[i].name+'</span><span class="btn-remove" data-dz="'+listId+'" data-idx="'+i+'"><i class="fa fa-times"></i></span></div>';
            }
        }
        $(document).on('click', '[data-dz="'+listId+'"]', function() {
            var idx = $(this).data('idx'), newDt = new DataTransfer();
            for(var i=0; i<dt.files.length; i++) { if(i !== idx) newDt.items.add(dt.files[i]); }
            dt = newDt; inp.files = dt.files; render();
        });
    }

    initDropzone('dok-dropzone','dok-input','dok-file-list', ['jpg','jpeg','png'], 5*1024*1024);
    initDropzone('mat-dropzone','mat-input','mat-file-list', ['doc','docx','pdf','ppt','pptx'], 2*1024*1024);

    // Upload dokumentasi
    $('#form-dokumentasi').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        $.ajax({ 
            url:"{{ route('agenda-rapat.store-dokumentasi', $data->id) }}", 
            type:'POST', 
            data:fd, 
            processData:false, 
            contentType:false,
            success: function(res) { 
                swal(res.status?'Berhasil!':'Gagal!', res.message, res.status?'success':'error'); 
                if(res.status) {
                    sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-dokumentasi');
                    setTimeout(function(){ location.reload(); }, 1500); 
                }
            },
            error: function() { swal('Error!','Terjadi kesalahan.','error'); }
        });
    });

    // Upload materi
    $('#form-materi').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        $.ajax({ 
            url:"{{ route('agenda-rapat.store-materi', $data->id) }}", 
            type:'POST', 
            data:fd, 
            processData:false, 
            contentType:false,
            success: function(res) { 
                swal(res.status?'Berhasil!':'Gagal!', res.message, res.status?'success':'error'); 
                if(res.status) {
                    sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-materi');
                    setTimeout(function(){ location.reload(); }, 1500); 
                }
            },
            error: function() { swal('Error!','Terjadi kesalahan.','error'); }
        });
    });

    // ===== TANDA TANGAN ELEKTRONIK (BSrE) =====

    // Toggle passphrase visibility
    $('#toggle-passphrase').on('click', function() {
        var inp = $('#input-passphrase');
        var icon = $(this).find('i');
        if (inp.attr('type') === 'password') {
            inp.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            inp.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Buka modal passphrase
    $(document).on('click', '.btn-sign-dokumen', function() {
        var jenis = $(this).data('jenis');
        var agendaId = $(this).data('agenda');
        $('#sign-jenis').val(jenis);
        $('#sign-agenda-id').val(agendaId);
        $('#input-passphrase').val('').attr('type', 'password');
        $('#toggle-passphrase i').removeClass('fa-eye-slash').addClass('fa-eye');
        $('#modal-passphrase').modal('show');
    });

    // Submit tanda tangan
    $('#form-sign-dokumen').on('submit', function(e) {
        e.preventDefault();
        var agendaId = $('#sign-agenda-id').val();
        var jenis = $('#sign-jenis').val();
        var passphrase = $('#input-passphrase').val();

        if (!passphrase) {
            swal('Perhatian!', 'Passphrase wajib diisi.', 'warning');
            return;
        }

        var jenisLabel = jenis.replace('_', ' ');
        jenisLabel = jenisLabel.charAt(0).toUpperCase() + jenisLabel.slice(1);

        swal({
            title: 'Konfirmasi Tanda Tangan',
            text: 'Anda yakin ingin menandatangani dokumen ' + jenisLabel + ' secara elektronik menggunakan BSrE?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tanda Tangani!',
            cancelButtonText: 'Batal',
            closeOnConfirm: false
        }, function(confirmed) {
            if (confirmed) {
                swal.close();
                $.showLoading();
                $.ajax({
                    url: '{{ url(config("mvc.route_prefix")) }}/agenda-rapat/' + agendaId + '/sign/' + jenis,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        passphrase: passphrase
                    },
                    success: function(res) {
                        $.hideLoading();
                        $('#modal-passphrase').modal('hide');
                        if (res.status) {
                            swal('Berhasil!', res.message, 'success');
                            var targetTab = '#tab-undangan';
                            if (jenis === 'daftar_hadir') targetTab = '#tab-absensi';
                            else if (jenis.indexOf('notulen') === 0) targetTab = '#tab-notulen';
                            sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", targetTab);
                            setTimeout(function() { location.reload(); }, 1500);
                        } else {
                            swal('Gagal!', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        $.hideLoading();
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan saat menandatangani dokumen.';
                        swal('Error!', msg, 'error');
                    }
                });
            }
        });
    });
});

window.refreshAbsensi = function() {
    sessionStorage.setItem('activeTab_agenda_' + "{{ $data->id }}", '#tab-absensi');
    window.location.reload();
};
</script>
@endpush
