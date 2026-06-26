@extends('frontend.main')

@push('title')
    {{ $title }}
@endpush

@section('container')
<section class="section pt-5" style="min-height: 80vh; margin-top: 80px; position: relative; z-index: 2;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <!-- Main Card -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-5">
                    
                    <!-- Header -->
                    <div class="card-header bg-gradient bg-success text-white py-4 px-4 text-center" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-bottom: none;">
                        <div class="mb-3">
                            <img src="{{ asset('eduadmin/images/inhu.png') }}" alt="Logo Inhu" style="height: 65px; width: auto; background: white; padding: 5px; border-radius: 8px;">
                        </div>
                        <h4 class="fw-bold mb-1 text-white"><i class="bi bi-patch-check-fill me-2"></i>Verifikasi Dokumen TTE</h4>
                        <p class="mb-0 opacity-90 text-white" style="font-size: 14px;">Sistem Informasi Hubungan Keprotokolan & Dokumentasi Rapat (Diskominfotik Indragiri Hulu)</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 bg-white">
                        
                        <!-- Status Alert -->
                        <div class="alert alert-success d-flex align-items-center rounded-3 p-3 mb-4 border-0" role="alert" style="background-color: rgba(16, 185, 129, 0.1); color: #047857;">
                            <i class="bi bi-shield-fill-check fs-2 me-3"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Dokumen Asli & Valid</h6>
                                <p class="mb-0 fs-7 text-success" style="font-size: 13px;">Dokumen ini telah ditandatangani secara elektronik (TTE) menggunakan sertifikat digital dari Balai Sertifikasi Elektronik (BSrE) Badan Siber dan Sandi Negara (BSSN).</p>
                            </div>
                        </div>

                        <!-- Agenda Rapat Section -->
                        <div class="mb-5">
                            <h5 class="fw-bold border-bottom pb-2 text-primary"><i class="bi bi-calendar-event me-2"></i>Informasi Agenda Rapat</h5>
                            <div class="row mt-3 g-3">
                                <div class="col-sm-4 fw-semibold text-muted">Nama Agenda</div>
                                <div class="col-sm-8 text-dark fw-bold">: {{ $data->nama }}</div>
                                
                                <div class="col-sm-4 fw-semibold text-muted">Hari / Tanggal</div>
                                <div class="col-sm-8 text-dark">: {{ $data->tanggal->translatedFormat('l, d F Y') }}</div>
                                
                                <div class="col-sm-4 fw-semibold text-muted">Waktu Rapat</div>
                                <div class="col-sm-8 text-dark">: {{ substr($data->jam_mulai,0,5) }} - {{ substr($data->jam_selesai,0,5) }} WIB</div>
                                
                                <div class="col-sm-4 fw-semibold text-muted">Tempat</div>
                                <div class="col-sm-8 text-dark">: {{ $data->tempat }}</div>
                            </div>
                        </div>

                        <!-- Status TTE per Dokumen -->
                        <div class="mb-4">
                            <h5 class="fw-bold border-bottom pb-2 mb-4 text-primary">
                                <i class="bi bi-file-earmark-check me-2"></i>
                                @if($jenis === 'undangan')
                                    Status Verifikasi Surat Undangan
                                @elseif($jenis === 'daftar_hadir')
                                    Status Verifikasi Daftar Hadir
                                @elseif($jenis === 'notulen')
                                    Status Verifikasi Notulen Rapat
                                @else
                                    Status Verifikasi Dokumen Digital
                                @endif
                            </h5>
                            
                            <!-- 1. SURAT UNDANGAN -->
                            @if(!$jenis || $jenis === 'undangan')
                            <div class="card border-0 bg-light rounded-3 p-3 mb-3 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-envelope-open me-2"></i>1. Surat Undangan Rapat</h6>
                                    @if($tteUndangan && $tteUndangan->status === 'signed')
                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi TTE</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Manual / Belum TTE</span>
                                    @endif
                                </div>
                                <div class="row g-2 mb-3 text-dark" style="font-size: 13px;">
                                    <div class="col-sm-4 text-muted">Penanda Tangan</div>
                                    <div class="col-sm-8">: {{ $data->pegawai->nama ?? '-' }}</div>
                                    
                                    <div class="col-sm-4 text-muted">Jabatan</div>
                                    <div class="col-sm-8">: {{ $data->pegawai->jabatanNama ? $data->pegawai->jabatanNama->nama : '-' }}</div>
                                    
                                    <div class="col-sm-4 text-muted">NIP</div>
                                    <div class="col-sm-8">: {{ $data->pegawai->nip ?? '-' }}</div>
                                    
                                    <div class="col-sm-4 text-muted">Waktu TTE</div>
                                    <div class="col-sm-8">: {{ $tteUndangan && $tteUndangan->signed_at ? \Carbon\Carbon::parse($tteUndangan->signed_at)->locale('id')->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('rapat.view-signed-pdf', [$data->barcode_token, 'undangan']) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-file-pdf me-1"></i>Lihat Surat Undangan Digital
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- 2. DAFTAR HADIR -->
                            @if(!$jenis || $jenis === 'daftar_hadir')
                            <div class="card border-0 bg-light rounded-3 p-3 mb-3 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill me-2"></i>2. Daftar Hadir Rapat</h6>
                                    @if($tteDaftarHadir && $tteDaftarHadir->status === 'signed')
                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi TTE</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Manual / Belum TTE</span>
                                    @endif
                                </div>
                                <div class="row g-2 mb-3 text-dark" style="font-size: 13px;">
                                    <div class="col-sm-4 text-muted">Penanda Tangan</div>
                                    <div class="col-sm-8">: {{ $data->pegawai->nama ?? '-' }}</div>
                                    
                                    <div class="col-sm-4 text-muted">Jabatan</div>
                                    <div class="col-sm-8">: {{ $data->pegawai->jabatanNama ? $data->pegawai->jabatanNama->nama : '-' }}</div>
                                    
                                    <div class="col-sm-4 text-muted">NIP</div>
                                    <div class="col-sm-8">: {{ $data->pegawai->nip ?? '-' }}</div>
                                    
                                    <div class="col-sm-4 text-muted">Waktu TTE</div>
                                    <div class="col-sm-8">: {{ $tteDaftarHadir && $tteDaftarHadir->signed_at ? \Carbon\Carbon::parse($tteDaftarHadir->signed_at)->locale('id')->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('rapat.view-signed-pdf', [$data->barcode_token, 'daftar_hadir']) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-file-pdf me-1"></i>Lihat Daftar Hadir Digital
                                    </a>
                                </div>
                            </div>
                            @endif

                            <!-- 3. NOTULEN RAPAT -->
                            @if(!$jenis || $jenis === 'notulen')
                            <div class="card border-0 bg-light rounded-3 p-3 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-card-text me-2"></i>3. Notulen Rapat</h6>
                                    @if($tteNotulenPimpinan && $tteNotulenPimpinan->status === 'signed')
                                        <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi TTE</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Belum Final / Menunggu TTE</span>
                                    @endif
                                </div>
                                
                                <div class="row g-3 text-dark mb-4" style="font-size: 13px;">
                                    <!-- Notulis -->
                                    <div class="col-md-6 border-end pr-md-3">
                                        <div class="fw-bold text-primary mb-2" style="font-size: 11px; text-transform: uppercase;">Penandatangan 1: Notulis</div>
                                        <div class="row g-1">
                                            <div class="col-5 text-muted mb-0">Nama</div>
                                            <div class="col-7 text-dark mb-0">: {{ $data->notulen->notulis ?? '-' }}</div>
                                            <div class="col-5 text-muted mb-0">Jabatan</div>
                                            <div class="col-7 text-dark mb-0">: {{ $notulis && $notulis->jabatanNama ? $notulis->jabatanNama->nama : '-' }}</div>
                                            <div class="col-5 text-muted mb-0">NIP</div>
                                            <div class="col-7 text-dark mb-0">: {{ $notulis->nip ?? '-' }}</div>
                                            <div class="col-5 text-muted mb-0">Status</div>
                                            <div class="col-7 mb-0">: 
                                                @if($tteNotulenNotulis && $tteNotulenNotulis->status === 'signed')
                                                    <span class="text-success fw-bold">SIGNED</span>
                                                @else
                                                    <span class="text-muted">BELUM TTE</span>
                                                @endif
                                            </div>
                                            <div class="col-5 text-muted mb-0">Waktu TTE</div>
                                            <div class="col-7 text-dark mb-0">: {{ $tteNotulenNotulis && $tteNotulenNotulis->signed_at ? \Carbon\Carbon::parse($tteNotulenNotulis->signed_at)->locale('id')->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Pimpinan -->
                                    <div class="col-md-6 pl-md-3">
                                        <div class="fw-bold text-primary mb-2" style="font-size: 11px; text-transform: uppercase;">Penandatangan 2: Pimpinan</div>
                                        <div class="row g-1">
                                            <div class="col-5 text-muted mb-0">Nama</div>
                                            <div class="col-7 text-dark mb-0">: {{ $data->notulen->pimpinan_rapat ?? '-' }}</div>
                                            <div class="col-5 text-muted mb-0">Jabatan</div>
                                            <div class="col-7 text-dark mb-0">: {{ $pimpinan && $pimpinan->jabatanNama ? $pimpinan->jabatanNama->nama : '-' }}</div>
                                            <div class="col-5 text-muted mb-0">NIP</div>
                                            <div class="col-7 text-dark mb-0">: {{ $pimpinan->nip ?? '-' }}</div>
                                            <div class="col-5 text-muted mb-0">Status</div>
                                            <div class="col-7 mb-0">: 
                                                @if($tteNotulenPimpinan && $tteNotulenPimpinan->status === 'signed')
                                                    <span class="text-success fw-bold">SIGNED</span>
                                                @else
                                                    <span class="text-muted">BELUM TTE</span>
                                                @endif
                                            </div>
                                            <div class="col-5 text-muted mb-0">Waktu TTE</div>
                                            <div class="col-7 text-dark mb-0">: {{ $tteNotulenPimpinan && $tteNotulenPimpinan->signed_at ? \Carbon\Carbon::parse($tteNotulenPimpinan->signed_at)->locale('id')->translatedFormat('d F Y H:i') . ' WIB' : '-' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    @if($data->notulen)
                                        <a href="{{ route('rapat.view-signed-pdf', [$data->barcode_token, 'notulen_pimpinan']) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-file-pdf me-1"></i>Lihat Notulen Digital
                                        </a>
                                    @else
                                        <button disabled class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bi bi-file-pdf me-1"></i>Notulen Belum Dibuat
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        </div>
                        
                    </div>
                    
                    <!-- Footer -->
                    <div class="card-footer bg-light py-3 text-center border-0">
                        <span class="text-muted" style="font-size: 12px;">&copy; {{ date('Y') }} Dinas Komunikasi, Informatika dan Statistik Kabupaten Indragiri Hulu</span>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>
</section>
@endsection
