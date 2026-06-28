<div class="modal-header">
    <h4 class="modal-title" id="modal-title-label">
        <i class="fa fa-calendar-check-o text-info me-10"></i> Log Kehadiran Harian Pegawai ({{ $source === 'live' ? 'Live BKN API' : 'Lokal DB' }})
    </h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-25">
    <style>
        .transition-hover { transition: all 0.3s ease; }
        .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
    
    <div class="row mb-25">
        <!-- Kolom Kiri: Informasi Pegawai -->
        <div class="col-lg-6 col-12 mb-15 mb-lg-0">
            <div class="box border shadow-sm mb-0 rounded-3 overflow-hidden bg-white">
                <div class="row g-0">
                    <!-- Foto -->
                    <div class="col-12 col-sm-5 bg-light d-flex align-items-center justify-content-center">
                        <img class="img-fluid" 
                             src="{{ url(config('master.app.url.backend').'/presensi/image/'.$pegawai->nip) }}" 
                             alt="Foto Pegawai" style="object-fit: contain; max-height: 500px;">
                    </div>
                    <!-- Informasi -->
                    <div class="col-12 col-sm-7 d-flex flex-column justify-content-center">
                        <div class="p-15">
                            <h5 class="text-primary font-weight-bold mb-10" style="line-height: 1.4;">
                                {{ ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') . $pegawai->nama . ($pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '') }}
                            </h5>
                            <p class="text-muted mb-5 font-size-13"><i class="fa fa-id-badge text-info me-5"></i> NIP. {{ $pegawai->nip ?? '-' }}</p>
                            <p class="text-muted mb-15 font-size-13"><i class="fa fa-briefcase text-info me-5"></i> {{ $pegawai->jabatanNama->nama ?? '-' }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center bg-light p-10 rounded mb-10 border">
                                <span class="font-size-12 text-muted font-weight-bold">Periode</span>
                                <span class="font-size-13 text-dark font-weight-bold">{{ $monthName }} {{ $year }}</span>
                            </div>

                            <div class="row text-center mb-2">
                                <div class="col-4 mb-2">
                                    <div class="bg-success-light p-10 rounded border border-success border-opacity-25 transition-hover">
                                        <h6 class="text-success mb-5 font-weight-bold font-size-13">Hadir (HN)</h6>
                                        <h3 class="mb-0 text-success font-weight-bold">{{ $countHn }} <small class="font-size-12">Hari</small></h3>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="bg-danger-light p-10 rounded border border-danger border-opacity-25 transition-hover">
                                        <h6 class="text-danger mb-5 font-weight-bold font-size-13">Alpa (TK)</h6>
                                        <h3 class="mb-0 text-danger font-weight-bold">{{ $countTk }} <small class="font-size-12">Hari</small></h3>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="bg-primary-light p-10 rounded border border-primary border-opacity-25 transition-hover">
                                        <h6 class="text-primary mb-5 font-weight-bold font-size-13">DL / TB</h6>
                                        <h3 class="mb-0 text-primary font-weight-bold">{{ $countDl }} <small class="font-size-12">Hari</small></h3>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="bg-info-light p-10 rounded border border-info border-opacity-25 transition-hover">
                                        <h6 class="text-info mb-5 font-weight-bold font-size-13">Cuti</h6>
                                        <h3 class="mb-0 text-info font-weight-bold">{{ $countCt }} <small class="font-size-12">Hari</small></h3>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="bg-warning-light p-10 rounded border border-warning border-opacity-25 transition-hover">
                                        <h6 class="text-warning mb-5 font-weight-bold font-size-13">Izin</h6>
                                        <h3 class="mb-0 text-warning font-weight-bold">{{ $countIzin }} <small class="font-size-12">Hari</small></h3>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="bg-dark-light p-10 rounded border border-dark border-opacity-25 transition-hover">
                                        <h6 class="text-dark mb-5 font-weight-bold font-size-13">Hari Kerja</h6>
                                        <h3 class="mb-0 text-dark font-weight-bold">{{ $totalHariKerja }} <small class="font-size-12">Hari</small></h3>
                                    </div>
                                </div>
                            </div>
                            <!-- Info Keterlambatan & Pulang Cepat -->
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center bg-warning-light p-10 rounded border border-warning border-opacity-25 transition-hover">
                                        <div class="me-15 bg-white p-10 rounded-circle shadow-sm"><i class="fa fa-clock-o fa-lg text-warning"></i></div>
                                        <div>
                                            <h6 class="mb-0 text-dark font-weight-bold font-size-13">Telat Masuk</h6>
                                            <span class="font-size-15 text-warning font-weight-bold">{{ $countTm }} <small class="text-muted font-size-12">Kali</small></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center bg-danger-light p-10 rounded border border-danger border-opacity-25 transition-hover">
                                        <div class="me-15 bg-white p-10 rounded-circle shadow-sm"><i class="fa fa-sign-out fa-lg text-danger"></i></div>
                                        <div>
                                            <h6 class="mb-0 text-dark font-weight-bold font-size-13">Pulang Cepat</h6>
                                            <span class="font-size-15 text-danger font-weight-bold">{{ $countPc }} <small class="text-muted font-size-12">Kali</small></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center bg-light p-10 rounded border">
                                <span class="font-size-12 text-muted font-weight-bold">Potongan</span>
                                @php
                                    $totalPotongan = $logs->sum('total_potongan');
                                @endphp
                                @if($totalPotongan > 0)
                                    <span class="badge badge-danger text-white fw-bold px-10 py-5 font-size-14 shadow-sm">-{{ number_format($totalPotongan, 2) }}%</span>
                                @else
                                    <span class="badge badge-success fw-bold px-10 py-5 font-size-14 shadow-sm">0.00%</span>
                                @endif
                            </div>

                        </div> 
                    </div>
                </div>  
            </div>
        </div>

        <!-- Kolom Kanan: Statistik Kehadiran -->
        <div class="col-lg-6 col-12">
            <div class="box border shadow-sm mb-0 rounded-3">
                <div class="box-body p-20 bg-white">
                    <h5 class="text-dark font-weight-bold mb-20 pb-10 border-bottom"><i class="fa fa-bar-chart text-primary me-10"></i> Statistik Kehadiran Pegawai</h5>
                    


                    <!-- SVG Donut Chart -->


                    <div class="d-flex flex-column align-items-center justify-content-center py-10">
                        <svg viewBox="0 0 36 36" class="circular-chart" id="attendance-donut">
                            <!-- Background -->
                            <path class="circle-bg"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            
                            <!-- Izin -->
                            @if($pctIzin > 0)
                            <path class="donut-segment color-izin"
                                data-label="Izin" data-val="{{ $countIzin }} Hari" data-color="#ffc107"
                                stroke-dasharray="{{ $pctIzin }}, 100" stroke-dashoffset="{{ $offsetIzin }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif
                            
                            <!-- Cuti -->
                            @if($pctCt > 0)
                            <path class="donut-segment color-ct"
                                data-label="Cuti" data-val="{{ $countCt }} Hari" data-color="#0dcaf0"
                                stroke-dasharray="{{ $pctCt }}, 100" stroke-dashoffset="{{ $offsetCt }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif

                            <!-- DL -->
                            @if($pctDl > 0)
                            <path class="donut-segment color-dl"
                                data-label="DL / TB" data-val="{{ $countDl }} Hari" data-color="#0d6efd"
                                stroke-dasharray="{{ $pctDl }}, 100" stroke-dashoffset="{{ $offsetDl }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif

                            <!-- Alpa -->
                            @if($pctTk > 0)
                            <path class="donut-segment color-tk"
                                data-label="Alpa (TK)" data-val="{{ $countTk }} Hari" data-color="#dc3545"
                                stroke-dasharray="{{ $pctTk }}, 100" stroke-dashoffset="{{ $offsetTk }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif

                            <!-- Pulang Cepat -->
                            @if($pctPc > 0)
                            <path class="donut-segment color-pc"
                                data-label="Pulang Cepat" data-val="{{ $countPc }} Kali" data-color="#ffc107"
                                stroke-dasharray="{{ $pctPc }}, 100" stroke-dashoffset="{{ $offsetPc }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif

                            <!-- Telat Masuk -->
                            @if($pctTm > 0)
                            <path class="donut-segment color-tm"
                                data-label="Telat Masuk" data-val="{{ $countTm }} Kali" data-color="#ffc107"
                                stroke-dasharray="{{ $pctTm }}, 100" stroke-dashoffset="{{ $offsetTm }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif

                            <!-- Hadir -->
                            @if($pctHn > 0)
                            <path class="donut-segment color-hn"
                                data-label="Hadir Normal" data-val="{{ $daysTepatWaktu }} Hari" data-color="#28a745"
                                stroke-dasharray="{{ $pctHn }}, 100" stroke-dashoffset="{{ $offsetHn }}"
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @endif

                            <!-- Center Text -->
                            <text x="18" y="16.5" class="percentage" id="donut-center-val" style="fill: #28a745;">{{ $persentaseEfektif }}%</text>
                            <text x="18" y="20.5" class="subtext" id="donut-center-label">Kehadiran Efektif</text>
                            <text x="18" y="24" class="subtext font-weight-bold" style="fill:#495057;">({{ $kehadiranEfektif }} / {{ $totalHariKerja }} Hari)</text>
                        </svg>
                        
                        <!-- Horizontal Legend -->
                        <div class="d-flex justify-content-start justify-content-md-center mt-15 flex-nowrap overflow-auto pb-2 px-1" style="gap: 5px; max-width: 100%;">
                            @if($daysTepatWaktu > 0)
                            <span class="badge badge-success px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-check"></i> Hadir ({{ $daysTepatWaktu }})</span>
                            @endif
                            
                            @if($countTm > 0)
                            <span class="badge badge-warning text-white px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-clock-o"></i> TM ({{ $countTm }}x)</span>
                            @endif
                            
                            @if($countPc > 0)
                            <span class="badge badge-warning text-white px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-sign-out"></i> PC ({{ $countPc }}x)</span>
                            @endif
                            
                            @if($countTk > 0)
                            <span class="badge badge-danger px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-times"></i> TK ({{ $countTk }})</span>
                            @endif
                            
                            @if($countDl > 0)
                            <span class="badge badge-primary px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-briefcase"></i> DL ({{ $countDl }})</span>
                            @endif
                            
                            @if($countCt > 0)
                            <span class="badge badge-info text-white px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-bed"></i> CT ({{ $countCt }})</span>
                            @endif
                            
                            @if($countIzin > 0)
                            <span class="badge badge-warning text-dark px-5 py-5 shadow-sm text-nowrap font-size-11 transition-hover"><i class="fa fa-envelope"></i> Izin ({{ $countIzin }})</span>
                            @endif
                        </div>
                    </div>

                    <script>
                        (function() {
                            var segments = document.querySelectorAll('#attendance-donut .donut-segment');
                            var centerVal = document.getElementById('donut-center-val');
                            var centerLabel = document.getElementById('donut-center-label');
                            
                            var defaultVal = '{{ $persentaseEfektif }}%';
                            var defaultLabel = 'Kehadiran Efektif';
                            var defaultColor = '#28a745';

                            segments.forEach(function(segment) {
                                segment.addEventListener('mouseenter', function() {
                                    var label = this.getAttribute('data-label');
                                    var val = this.getAttribute('data-val');
                                    var color = this.getAttribute('data-color');
                                    
                                    centerVal.textContent = val;
                                    centerLabel.textContent = label;
                                    centerVal.style.fill = color;
                                    
                                    segments.forEach(function(s) {
                                        if (s !== segment) s.style.opacity = '0.2';
                                    });
                                });
                                
                                segment.addEventListener('mouseleave', function() {
                                    centerVal.textContent = defaultVal;
                                    centerLabel.textContent = defaultLabel;
                                    centerVal.style.fill = defaultColor;
                                    
                                    segments.forEach(function(s) {
                                        s.style.opacity = '1';
                                    });
                                });
                            });
                        })();
                    </script>

                    
                </div>
            </div>
        </div>
    </div>


    <!-- Tabel Riwayat Kehadiran Harian -->
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="bg-dark text-white sticky-top">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th class="text-center" style="width: 180px;">Tanggal</th>
                    <th class="text-center" style="width: 100px;">Status</th>
                    <th class="text-center" style="width: 90px;">Masuk</th>
                    <th class="text-center" style="width: 90px;">Pulang</th>
                    <th class="text-center" style="width: 130px;">Lama Terlambat</th>
                    <th class="text-center" style="width: 130px;">Pulang Cepat</th>
                    <th class="text-center" style="width: 90px;">Potongan</th>
                    <th>Keterangan</th>
                    <th class="text-center" style="width: 90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    @php
                        $statusHarian = $log->status_kehadiran;
                        $isWeekendOrHoliday = in_array($statusHarian, ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF']);
                    @endphp
                    <tr class="{{ $isWeekendOrHoliday ? 'table-light' : '' }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center font-weight-bold" style="white-space: nowrap;">{{ $log->tanggal->translatedFormat('l, d F Y') }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = \App\Models\PresensiHarian::getStatusBadgeClass($statusHarian);
                            @endphp
                            <span class="badge {{ $badgeClass }} font-weight-bold">{{ $statusHarian }}</span>
                        </td>
                        <td class="text-center font-monospace">
                            @if($log->work_from_masuk)
                                <span class="badge badge-light border d-block mb-1 font-size-11">{{ $log->work_from_masuk }}</span>
                            @endif
                            {{ $log->jam_masuk ?? '-' }}
                        </td>
                        <td class="text-center font-monospace">
                            @if($log->work_from_keluar)
                                <span class="badge badge-light border d-block mb-1 font-size-11">{{ $log->work_from_keluar }}</span>
                            @endif
                            {{ $log->jam_keluar ?? '-' }}
                        </td>
                        <td class="text-center">
                            @if($log->kategori_terlambat)
                                <span class="badge badge-warning text-white font-weight-bold">{{ $log->kategori_terlambat }}</span>
                                <small class="text-muted d-block mt-2">({{ $log->menit_terlambat }} menit)</small>
                            @else
                                @if(!$isWeekendOrHoliday && !in_array($statusHarian, ['TK', 'DL', 'IDLI', 'ITM']))
                                    <span class="text-success"><i class="fa fa-check-circle"></i> Tepat Waktu</span>
                                @else
                                    -
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            @if($log->kategori_pulang_cepat)
                                <span class="badge badge-warning text-white font-weight-bold">{{ $log->kategori_pulang_cepat }}</span>
                                <small class="text-muted d-block mt-2">({{ $log->menit_pulang_cepat }} menit)</small>
                            @else
                                @if(!$isWeekendOrHoliday && !in_array($statusHarian, ['TK', 'DL', 'IDLI', 'ITM']))
                                    <span class="text-success"><i class="fa fa-check-circle"></i> Sesuai Jadwal</span>
                                @else
                                    -
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            @if($log->total_potongan > 0)
                                <span class="text-danger font-weight-bold">-{!! number_format($log->total_potongan, 2) !!}%</span>
                            @else
                                <span class="text-success">0.00%</span>
                            @endif
                        </td>
                        <td><span class="font-size-13">{{ $log->keterangan ?? '-' }}</span></td>
                        <td class="text-center">
                            @if(!$isWeekendOrHoliday && ($log->jam_masuk || $log->jam_keluar))
                                <button type="button" class="btn btn-xs btn-outline btn-info btn-action" 
                                        data-title="Geolocation & Info Detil" 
                                        data-action="" 
                                        data-size="modal-lg" 
                                        data-modal-id="modal-nested" 
                                        data-url="{{ url(config('master.app.url.backend').'/presensi/riwayat/'.$pegawai->nip.'/'.$log->tanggal->format('Y-m-d')) }}">
                                    <i class="fa fa-map-marker"></i> Lokasi
                                </button>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-30 text-muted">
                            <i class="fa fa-exclamation-triangle fa-2x d-block mb-10 text-warning"></i>
                            Belum ada riwayat kehadiran harian yang disinkronkan untuk bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
</div>
