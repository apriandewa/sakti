@extends('backend.main.index')
@push('title', $page->title ?? 'Data Pegawai')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{{ $page->icon }}"></i> {{ $page->title ?? 'Data Pegawai' }}
                        </h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"> {{ $page->subtitle ?? 'Welcome to '.$page->title.' page' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="box">
                    <div class="box-body">
                        <h4 class="box-title mb-3">Grafik Data Pegawai</h4>
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-statistik" data-bs-toggle="tab" data-bs-target="#tabStatistik" type="button" role="tab">Statistik</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-detail" data-bs-toggle="tab" data-bs-target="#tabDetail" type="button" role="tab">Detail Lainnya</button>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="tabStatistik" role="tabpanel">
                                <div class="row gx-4 gy-4 mb-4">
                                    <div class="col-lg-4 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/009-stats.svg') }}" alt="Total Pegawai" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Total Pegawai</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalPegawai ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="150">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/004-dad.svg') }}" alt="Pegawai Laki-laki" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Jumlah Pegawai Laki-laki</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalPegawaiLaki ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/005-paint-palette.svg') }}" alt="Pegawai Perempuan" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Jumlah Pegawai Perempuan</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalPegawaiPerempuan ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="250">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/003-settings.svg') }}" alt="PNS" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Jumlah PNS</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalPNS ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/010-refresh.svg') }}" alt="CPNS" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Jumlah CPNS</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalCPNS ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="350">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/007-color-palette.svg') }}" alt="PPPK" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Jumlah PPPK</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalPPPK ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="card-title d-flex align-items-start justify-content-between">
                                                    <div class="avatar flex-shrink-0">
                                                        <img src="{{ url($template.'/images/svg-icon/color-svg/006-pottery.svg') }}" alt="PPPK-PW" class="rounded" />
                                                    </div>
                                                </div>
                                                <span>Jumlah PPPK-PW</span>
                                                <h3 class="card-title text-nowrap mb-1 text-warning"><span class="purecounter" data-purecounter-start="0" data-purecounter-end="{{ $totalPPPKPW ?? 0 }}" data-purecounter-duration="1"></span></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabDetail" role="tabpanel">
                                <div class="row gx-4 gy-4">
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <div class="card border-primary h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Jenis Kelamin</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartGender" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <div class="card border-success h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Agama</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartAgama" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-md-12">
                                        <div class="card border-warning h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Status</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartStatus" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="card border-info h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Pangkat</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartPangkat" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="card border-secondary h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Jenis Jabatan</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartJabatanJenis" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="card border-dark h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Nama Jabatan</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartJabatanNama" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="card border-primary h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Bidang</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartBidang" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="card border-success h-100" style="min-height: 320px;">
                                            <div class="card-body d-flex flex-column">
                                                <h4 class="card-title">Pendidikan Terakhir</h4>
                                                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                                    <canvas id="chartPendidikan" style="max-height: 240px; width: 100%;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row gx-4 gy-4">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header">
                                <h4 class="box-title">Daftar Pegawai</h4>
                                @if($user->create)
                                    <button type="button" class="btn-action pull-right btn btn-success btn-sm" data-title="Tambah" data-action="create" data-url="{!! $page->url ?? '' !!}">
                                        <span class="fa fa-plus-circle"></span> Tambah
                                    </button>
                                @endif
                            </div>
                            <div class="box-body">
                                <table id="datatable" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th class="w-0">No</th>
                                        <th>Nama Pegawai</th>
                                        <th>NIP / NIK</th>
                                        <th>Pangkat</th>
                                        <th>Jabatan</th>
                                        <th>Bidang</th>
                                        <th>Status Kerja</th>
                                        <th>Status Aktif</th>
                                        <th class="text-center w-0">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ url('portal/vendor/purecounter/purecounter_vanilla.js') }}"></script>
    <script src="{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>
    <script>
        (function(){
            const genderLabels = @json($pegawaiGenderLabels ?? []);
            const genderData = @json($pegawaiGenderData ?? []);

            const agamaLabels = @json($pegawaiAgamaLabels ?? []);
            const agamaData = @json($pegawaiAgamaData ?? []);

            const statusLabels = @json($pegawaiStatusLabels ?? []);
            const statusData = @json($pegawaiStatusData ?? []);

            const pangkatLabels = @json($pegawaiPangkatLabels ?? []);
            const pangkatData = @json($pegawaiPangkatData ?? []);

            const jabJenisLabels = @json($pegawaiJabatanJenisLabels ?? []);
            const jabJenisData = @json($pegawaiJabatanJenisData ?? []);

            const jabNamaLabels = @json($pegawaiJabatanNamaLabels ?? []);
            const jabNamaData = @json($pegawaiJabatanNamaData ?? []);

            const bidangLabels = @json($pegawaiBidangLabels ?? []);
            const bidangData = @json($pegawaiBidangData ?? []);

            const pendidikanLabels = @json($pegawaiPendidikanLabels ?? []);
            const pendidikanData = @json($pegawaiPendidikanData ?? []);

            function getTotal(data){
                return data.reduce((sum, value) => sum + Number(value), 0);
            }

            function formatPercent(value, total){
                return total === 0 ? '0%' : Math.round((value / total) * 100) + '%';
            }

            function legendLabelsWithPercent(chart, labels, data){
                const total = getTotal(data);
                return labels.map((label, index) => ({
                    text: `${label}: ${data[index]} (${formatPercent(data[index], total)})`,
                    fillStyle: chart.data.datasets[0].backgroundColor[index],
                    hidden: false,
                    lineCap: 'round',
                    datasetIndex: 0,
                    index
                }));
            }

            function defaultColors(n){
                const palette = ['#3b82f6','#06b6d4','#ef4444','#f59e0b','#10b981','#8b5cf6','#ec4899','#64748b','#f97316','#0ea5e9'];
                return Array.from({ length: n }, (_, i) => palette[i % palette.length]);
            }

            function createChart(id, type, labels, data, options = {}){
                const ctx = document.getElementById(id);
                if(!ctx) return;
                const colorSet = options.backgroundColor || defaultColors(labels.length);
                new Chart(ctx.getContext('2d'), {
                    type,
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah',
                            data,
                            backgroundColor: colorSet,
                            borderColor: options.borderColor || 'transparent',
                            borderWidth: 1,
                            fill: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    generateLabels: (chart) => legendLabelsWithPercent(chart, labels, data)
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => {
                                        const value = context.parsed;
                                        return `${context.label}: ${value} (${formatPercent(value, getTotal(data))})`;
                                    }
                                }
                            }
                        },
                        scales: options.scales || {}
                    }
                });
            }

            function staticColors(labels, mapping){
                return labels.map(label => mapping[label] || defaultColors(labels.length)[labels.indexOf(label) % labels.length]);
            }

            createChart('chartGender', 'pie', genderLabels, genderData, {
                backgroundColor: staticColors(genderLabels, {
                    'Laki-laki': '#2563eb',
                    'Perempuan': '#ec4899'
                })
            });

            createChart('chartAgama', 'pie', agamaLabels, agamaData);

            createChart('chartStatus', 'pie', statusLabels, statusData, {
                backgroundColor: staticColors(statusLabels, {
                    'PNS': '#10b981',
                    'CPNS': '#f97316',
                    'PPPK': '#3b82f6',
                    'PPPK-PW': '#60a5fa'
                })
            });

            createChart('chartPangkat', 'bar', pangkatLabels, pangkatData, {
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            });

            createChart('chartJabatanJenis', 'doughnut', jabJenisLabels, jabJenisData, {
                backgroundColor: defaultColors(jabJenisLabels.length)
            });

            createChart('chartJabatanNama', 'bar', jabNamaLabels, jabNamaData, {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            });

            createChart('chartBidang', 'doughnut', bidangLabels, bidangData, {
                backgroundColor: defaultColors(bidangLabels.length)
            });

            createChart('chartPendidikan', 'bar', pendidikanLabels, pendidikanData, {
                scales: {
                    x: { beginAtZero: true },
                    y: { beginAtZero: true }
                }
            });
            })();
            function initPureCounter() {
                if (typeof PureCounter !== 'undefined') {
                    new PureCounter();
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPureCounter);
            } else {
                initPureCounter();
            }
        </script>
@endpush
