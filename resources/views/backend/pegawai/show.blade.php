@extends('backend.main.index')
@push('title', ($page->title ?? 'Pegawai') . ' - Detail')
@section('content')
@php
    $fotoFile = $data->getfilebyalias('foto_pegawai');
    $tteFile = $data->getfilebyalias('spesimen_tte');
    $styling = $data->jabatan_styling;
    $dep = $data->gelar_depan ? $data->gelar_depan . ' ' : '';
    $bel = $data->gelar_belakang ? ', ' . $data->gelar_belakang : '';
    $fullName = $dep . $data->nama . $bel;
@endphp

<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="me-auto">
                    <h3 class="page-title"><i class="{!! $page->icon ?? 'fa fa-user' !!}"></i> Detail Pegawai</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('pegawai.index') }}">{!! $page->title ?? 'Pegawai' !!}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Detail</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <section class="content">
            <div class="row">
                <!-- Left Column (Profile & TTE Specs) -->
                <div class="col-lg-4 col-12">
                    <!-- Profile Card -->
                    <div class="box shadow-sm border-0 overflow-hidden profile-card mb-4" style="border-radius: 12px;">
                        <div class="profile-card-header" style="background: {!! $styling['gradient'] !!}; height: 100px; position: relative;">
                            <span class="badge position-absolute top-0 end-0 m-3 fw-bold" style="background-color: {!! $styling['badge_bg'] !!}; color: {!! $styling['badge_text'] !!}; font-size: 10px; border-radius: 4px; letter-spacing: 0.5px; text-transform: uppercase;">
                                {{ $data->jabatanJenis ? $data->jabatanJenis->nama : '-' }}
                            </span>
                        </div>
                        <div class="box-body text-center pt-0 position-relative">
                            <!-- Avatar (overlaps header) -->
                            <div class="profile-avatar-container mx-auto" style="width: 120px; height: 120px; margin-top: -60px; position: relative; z-index: 2;">
                                <div class="avatar-glow" style="position: absolute; inset: -4px; border-radius: 50%; border: 2px solid {!! $styling['border'] !!}; box-shadow: 0 0 15px {!! $styling['glow'] !!}; opacity: 0.8;"></div>
                                <img src="{{ $data->foto_url }}" alt="{{ $fullName }}" class="rounded-circle img-thumbnail bg-white" style="width: 120px; height: 120px; object-fit: cover; border: 2px solid {!! $styling['border'] !!}; position: relative; z-index: 3;">
                                @if($data->status == 'aktif')
                                    <span class="status-indicator bg-success" style="position: absolute; bottom: 8px; right: 8px; width: 16px; height: 16px; border-radius: 50%; border: 3px solid #fff; z-index: 4;" title="Aktif"></span>
                                @else
                                    <span class="status-indicator bg-danger" style="position: absolute; bottom: 8px; right: 8px; width: 16px; height: 16px; border-radius: 50%; border: 3px solid #fff; z-index: 4;" title="Tidak Aktif"></span>
                                @endif
                            </div>

                            <h4 class="mt-3 mb-1 fw-bold text-dark">{{ $fullName }}</h4>
                            <p class="text-semibold text-truncate mb-2 {!! $styling['text_class'] !!}" style="font-size: 13px;">
                                {{ $data->jabatanNama ? $data->jabatanNama->nama : '-' }}
                            </p>
                            
                            <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                                <span class="badge bg-light text-dark border border-secondary py-1 px-3" style="border-radius: 30px; font-size: 11px;">
                                    NIP. {{ $data->nip ?? '-' }}
                                </span>
                            </div>

                            <hr class="my-3">

                            <!-- Account Details -->
                            <div class="text-start mt-2">
                                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 10px; letter-spacing: 1px;">Akun Terhubung</h6>
                                @if($data->user)
                                    <div class="d-flex align-items-center p-2 rounded" style="background-color: #f8f9fa; border: 1px solid #e9ecef;">
                                        <div class="me-2 rounded-circle d-flex align-items-center justify-content-center bg-primary-light" style="width: 32px; height: 32px; color: #7070ba; background-color: rgba(112, 112, 186, 0.1);">
                                            <i class="fa fa-user-circle-o fa-lg"></i>
                                        </div>
                                        <div class="text-truncate" style="flex: 1;">
                                            <p class="mb-0 fw-semibold text-dark text-truncate" style="font-size: 12px;">{{ $data->user->name }}</p>
                                            <p class="mb-0 text-muted text-truncate" style="font-size: 11px;">{{ $data->user->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-2 rounded text-center text-muted" style="background-color: #f8f9fa; border: 1px dashed #ced4da; font-size: 11px;">
                                        <i class="fa fa-exclamation-triangle me-1"></i> Belum dihubungkan ke akun login
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- TTE Specimen Card -->
                    <div class="box shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                        <div class="box-header with-border py-3 bg-white">
                            <h5 class="box-title fw-bold text-dark" style="font-size: 14px;"><i class="fa fa-pencil-square-o text-primary me-2"></i> Spesimen TTE</h5>
                        </div>
                        <div class="box-body text-center p-4">
                            @if($tteFile && $tteFile->exists)
                                <div class="tte-specimen-wrapper p-3 rounded mx-auto" style="border: 1px solid #e2e8f0; background: radial-gradient(circle, #fafafa 10%, transparent 11%), radial-gradient(circle, #fafafa 10%, transparent 11%); background-size: 10px 10px; background-position: 0 0, 5px 5px; max-width: 200px;">
                                    <img src="{{ $tteFile->link_stream }}" alt="Spesimen TTE" class="img-fluid" style="max-height: 120px; object-fit: contain;">
                                </div>
                                <p class="text-success mt-2 mb-0 fw-semibold" style="font-size: 12px;"><i class="fa fa-check-circle"></i> Terdaftar</p>
                            @else
                                <div class="p-4 rounded border text-muted d-flex flex-column align-items-center justify-content-center" style="border-style: dashed !important; background-color: #fcfcfc;">
                                    <i class="fa fa-ban fa-2x mb-2 text-warning"></i>
                                    <span style="font-size: 12px;">Belum Ada Spesimen TTE</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column (Detail Tabs) -->
                <div class="col-lg-8 col-12">
                    <div class="box shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
                        <div class="box-header p-0 bg-white border-bottom">
                            <!-- Tabs Navigation -->
                            <ul class="nav nav-tabs custom-tabs px-3" id="pegawaiTabs" role="tablist" style="border-bottom: none;">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active py-3 px-4 fw-semibold text-dark border-0" id="kepegawaian-tab" data-bs-toggle="tab" href="#tab-kepegawaian" role="tab" aria-controls="tab-kepegawaian" aria-selected="true">
                                        <i class="fa fa-briefcase me-2 text-primary"></i> Informasi Kepegawaian
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link py-3 px-4 fw-semibold text-dark border-0" id="pribadi-tab" data-bs-toggle="tab" href="#tab-pribadi" role="tab" aria-controls="tab-pribadi" aria-selected="false">
                                        <i class="fa fa-user me-2 text-info"></i> Informasi Pribadi / Biodata
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link py-3 px-4 fw-semibold text-dark border-0" id="riwayat-tab" data-bs-toggle="tab" href="#tab-riwayat" role="tab" aria-controls="tab-riwayat" aria-selected="false">
                                        <i class="fa fa-history me-2 text-warning"></i> Riwayat Penandatanganan
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="box-body p-4 bg-white">
                            <div class="tab-content" id="pegawaiTabsContent">
                                <!-- Tab 1: Kepegawaian -->
                                <div class="tab-pane fade show active" id="tab-kepegawaian" role="tabpanel" aria-labelledby="kepegawaian-tab">
                                    <div class="row g-4">
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Nomor Induk Pegawai (NIP)</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->nip ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Status Kerja</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->statusPegawai ? $data->statusPegawai->nama : '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Pangkat / Golongan</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->pangkat ? $data->pangkat->nama : '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Jenis Jabatan</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->jabatanJenis ? $data->jabatanJenis->nama : '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Nama Jabatan</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->jabatanNama ? $data->jabatanNama->nama : '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Bidang / Bagian</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->bidang ? $data->bidang->nama : '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Periode</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->periode ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Status Keaktifan</label>
                                                <div>
                                                    @if($data->status == 'aktif')
                                                        <span class="badge badge-success px-3 py-1" style="font-size: 11px; border-radius: 30px;">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger px-3 py-1" style="font-size: 11px; border-radius: 30px;">Tidak Aktif</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2: Pribadi / Biodata -->
                                <div class="tab-pane fade" id="tab-pribadi" role="tabpanel" aria-labelledby="pribadi-tab">
                                    <div class="row g-4">
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Nomor Induk Kependudukan (NIK)</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->nik ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Nama Lengkap</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $fullName }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Jenis Kelamin</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->jenis_kelamin ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Agama</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->agama ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Pendidikan Terakhir</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $data->pendidikan_terakhir ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">No. Telepon / WhatsApp</label>
                                                <div class="fw-semibold text-dark" style="font-size: 14px;">
                                                    @if($data->telpon)
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $data->telpon) }}" target="_blank" class="text-decoration-none">
                                                            <i class="fa fa-whatsapp text-success me-1"></i> {{ $data->telpon }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-item mb-3">
                                                <label class="text-uppercase text-muted fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Alamat Rumah</label>
                                                <div class="text-dark" style="font-size: 14px; line-height: 1.6;">{{ $data->alamat ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 3: Riwayat Penandatanganan -->
                                <div class="tab-pane fade" id="tab-riwayat" role="tabpanel" aria-labelledby="riwayat-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="w-0 text-center">No</th>
                                                    <th>Nama Agenda / Rapat</th>
                                                    <th>Jenis Dokumen</th>
                                                    <th>Tanggal TTD</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center w-0">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($historyTte as $index => $tte)
                                                    <tr>
                                                        <td class="text-center fw-semibold text-muted">
                                                            {{ $historyTte->firstItem() + $index }}
                                                        </td>
                                                        <td>
                                                            @if($tte->agendaRapat)
                                                                <a href="{{ url('admin/agenda-rapat/' . $tte->agenda_rapat_id) }}" class="fw-semibold text-primary">
                                                                    {{ $tte->agendaRapat->nama }}
                                                                </a>
                                                                <div class="small text-muted">
                                                                    <i class="fa fa-calendar me-1"></i> {{ $tte->agendaRapat->tanggal ? $tte->agendaRapat->tanggal->format('d/m/Y') : '-' }}
                                                                </div>
                                                            @else
                                                                <span class="text-muted">Agenda tidak ditemukan / dihapus</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="text-capitalize text-dark fw-medium">
                                                                {{ str_replace('_', ' ', $tte->jenis_dokumen) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($tte->signed_at)
                                                                <span class="text-dark">{{ $tte->signed_at->format('d/m/Y') }}</span>
                                                                <div class="small text-muted">{{ $tte->signed_at->format('H:i') }} WIB</div>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            {!! $tte->badge_status !!}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($tte->status === 'signed')
                                                                <a href="{{ route('agenda-rapat.download-signed', [$tte->agenda_rapat_id, $tte->jenis_dokumen]) }}" class="btn btn-xs btn-success d-inline-flex align-items-center justify-content-center" target="_blank" title="Download Signed PDF">
                                                                    <i class="fa fa-download me-1"></i> PDF
                                                                </a>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4 text-muted">
                                                            <i class="fa fa-folder-open-o fa-2x mb-2 d-block"></i>
                                                            <span>Belum ada riwayat penandatanganan dokumen</span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination links with Bootstrap styling -->
                                    <div class="mt-4 d-flex justify-content-center">
                                        {!! $historyTte->appends(request()->input())->links('pagination::bootstrap-4') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('css')
<style>
    /* Premium Tab Navigation Styling */
    .custom-tabs .nav-link {
        border-bottom: 3px solid transparent !important;
        background-color: transparent !important;
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
        border-radius: 0 !important;
    }
    .custom-tabs .nav-link:hover {
        color: #7070ba !important;
        border-bottom: 3px solid #e2e8f0 !important;
    }
    .custom-tabs .nav-link.active {
        color: #7070ba !important;
        border-bottom: 3px solid #7070ba !important;
        font-weight: 700 !important;
    }
    
    /* Profile Avatar Ring Pulsing Glow */
    @keyframes avatarPulse {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.03); opacity: 0.5; }
        100% { transform: scale(1); opacity: 0.8; }
    }
    .avatar-glow {
        animation: avatarPulse 3s infinite ease-in-out;
    }

    /* Detail Grid Layout Info Styling */
    .detail-item {
        background-color: #fafbfd;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .detail-item:hover {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        transform: translateY(-1px);
    }
    .profile-card {
        transition: all 0.3s ease;
    }
    .profile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Automatically switch to the signature history tab if page query parameter is present
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('page')) {
            $('.nav-tabs a[href="#tab-riwayat"]').tab('show');
        }

        // Preserve tab across browser reloads
        var activeTab = sessionStorage.getItem('activeTab_pegawai_' + "{{ $data->id }}");
        if (activeTab && !urlParams.has('page')) {
            $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
        }

        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            sessionStorage.setItem('activeTab_pegawai_' + "{{ $data->id }}", $(e.target).attr('href'));
        });
    });
</script>
@endpush
