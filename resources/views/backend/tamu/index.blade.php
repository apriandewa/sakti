@extends('backend.main.index')
@push('title', $page->title ?? 'Tamu')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{!! $page->icon !!}"></i> {!! $page->title ?? 'Page Name' !!} </h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"> {!! $page->subtitle ?? 'Welcome to '.$page->title.' page' !!}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">

                {{-- ROW STATISTIK & GRAFIK --}}
                <div class="row">

                    {{-- KIRI : STATISTIK --}}
                    <div class="col-xl-4 col-12">

                        <div class="box">

                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    Statistik Jumlah Kunjungan Tamu
                                </h4>
                            </div>

                            <div class="box-body">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="box bg-primary">
                                            <div class="box-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="text-white">Total Kunjungan</h5>
                                                        <h2 class="text-white mb-0">
                                                            {{ number_format($statistik['total']) }}
                                                        </h2>
                                                    </div>

                                                    <i class="fa fa-users fa-3x text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="box bg-success">
                                            <div class="box-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="text-white">Hari Ini</h5>
                                                        <h2 class="text-white mb-0">
                                                            {{ number_format($statistik['hari_ini']) }}
                                                        </h2>
                                                    </div>

                                                    <i class="fa fa-calendar-day fa-3x text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="box bg-warning">
                                            <div class="box-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="text-white">Minggu Ini</h5>
                                                        <h2 class="text-white mb-0">
                                                            {{ number_format($statistik['minggu_ini'] ?? 0) }}
                                                        </h2>
                                                    </div>

                                                    <i class="fa fa-calendar-week fa-3x text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="box bg-info">
                                            <div class="box-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h5 class="text-white">Bulan Ini</h5>
                                                        <h2 class="text-white mb-0">
                                                            {{ number_format($statistik['bulan_ini']) }}
                                                        </h2>
                                                    </div>

                                                    <i class="fa fa-calendar-alt fa-3x text-white"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN : GRAFIK --}}
                    <div class="col-xl-8 col-12">

                        <div class="box">

                            <div class="box-header with-border">
                                <h4 class="box-title">
                                    Statistik Grafik Kunjungan Tamu
                                </h4>
                            </div>

                            <div class="box-body">

                                <div class="row mb-3">

                                    <div class="col-md-3">
                                        <label>Kategori Statistik</label>

                                        <select id="filterKategori" class="form-control">
                                            <option value="jenis_kelamin">Jenis Kelamin</option>
                                            <option value="pekerjaan">Pekerjaan</option>
                                            <option value="keperluan">Keperluan</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Kelompok Data</label>

                                        <select id="filterPeriode" class="form-control">
                                            <option value="bulanan">Bulanan</option>
                                            <option value="tahunan">Tahunan</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Tahun</label>

                                        <select id="filterTahun" class="form-control">
                                            @for($tahun = now()->year; $tahun >= 2020; $tahun--)
                                                <option value="{{ $tahun }}">
                                                    {{ $tahun }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Bulan</label>

                                        <select id="filterBulan" class="form-control">
                                            @for($bulan = 1; $bulan <= 12; $bulan++)
                                                <option value="{{ $bulan }}">
                                                    {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                </div>

                                <div style="height: 450px;">
                                    <canvas id="chartTamu"></canvas>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ROW TABLE --}}
                <div class="row">

                    <div class="col-12">

                        <div class="box">

                            <div class="box-header">
                                <h4 class="box-title">
                                    Content {!! $page->title ?? 'Page Name' !!}
                                </h4>

                                @if($user->create)
                                    <button
                                        type="button"
                                        class="btn-action pull-right btn btn-success btn-sm"
                                        data-title="Tambah"
                                        data-action="create"
                                        data-url="{!! $page->url ?? '' !!}"
                                    >
                                        <span class="fa fa-plus-circle"></span>
                                        Tambah
                                    </button>
                                @endif
                            </div>

                            <div class="box-body">

                                <table id="datatable"
                                    class="table table-bordered table-striped"
                                    style="width:100%">

                                    <thead>
                                        <tr>
                                            <th class="w-0">No</th>
                                            <th>Nama</th>
                                            <th>Pekerjaan</th>
                                            <th>Asal</th>
                                            <th>Keperluan</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th class="text-center w-0">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody></tbody>

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
    <script src="{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>
    <script>
    let chartTamu = null;

    function loadGrafik()
    {
        let kategori = $('#filterKategori').val();
        let periode  = $('#filterPeriode').val();
        let tahun    = $('#filterTahun').val();
        let bulan    = $('#filterBulan').val();

        $.ajax({

            url: "{{ route('tamu.grafik') }}",

            type: "GET",

            data: {
                kategori : kategori,
                periode  : periode,
                tahun    : tahun,
                bulan    : bulan
            },

            success: function(response)
            {

                let labels = [];
                let totals = [];

                /*
                |--------------------------------------------------------------------------
                | JIKA DATA KOSONG
                |--------------------------------------------------------------------------
                */

                if(response.length === 0)
                {
                    labels = ['Tidak Ada Data'];
                    totals = [0];
                }
                else
                {
                    response.forEach(function(item){

                        labels.push(item.label);

                        totals.push(item.total);

                    });
                }

                /*
                |--------------------------------------------------------------------------
                | WARNA DINAMIS
                |--------------------------------------------------------------------------
                */

                const colors = [
                    '#3B82F6',
                    '#10B981',
                    '#F59E0B',
                    '#EF4444',
                    '#8B5CF6',
                    '#06B6D4',
                    '#84CC16',
                    '#F97316',
                    '#EC4899',
                    '#14B8A6',
                    '#6366F1',
                    '#EAB308',
                    '#22C55E',
                    '#0EA5E9',
                    '#A855F7',
                    '#FB7185',
                    '#2DD4BF',
                    '#F87171',
                    '#4ADE80',
                    '#C084FC'
                ];

                const dynamicColors = labels.map((_, index) => {
                    return colors[index % colors.length];
                });

                /*
                |--------------------------------------------------------------------------
                | DESTROY CHART LAMA
                |--------------------------------------------------------------------------
                */

                if(chartTamu){
                    chartTamu.destroy();
                }

                const ctx = document.getElementById('chartTamu');

                /*
                |--------------------------------------------------------------------------
                | CHART BARU
                |--------------------------------------------------------------------------
                */

                chartTamu = new Chart(ctx, {

                    type: 'bar',

                    data: {

                        labels: labels,

                        datasets: [{

                            label: 'Jumlah Kunjungan',

                            data: totals,

                            backgroundColor: dynamicColors,

                            borderColor: dynamicColors,

                            borderWidth: 1,

                            borderRadius: 8,

                            maxBarThickness: 60

                        }]
                    },

                    options: {

                        responsive: true,

                        maintainAspectRatio: false,

                        animation: {
                            duration: 1000
                        },

                        plugins: {

                            legend: {
                                display: false
                            },

                            tooltip: {

                                callbacks: {

                                    label: function(context) {

                                        return 'Jumlah Kunjungan : ' + context.raw;

                                    }

                                }

                            }

                        },

                        scales: {

                            x: {

                                ticks: {

                                    font: {
                                        size: 12
                                    }

                                }

                            },

                            y: {

                                beginAtZero: true,

                                ticks: {

                                    precision: 0,

                                    stepSize: 1

                                }

                            }

                        }

                    },

                    /*
                    |--------------------------------------------------------------------------
                    | LABEL ANGKA DI ATAS BAR
                    |--------------------------------------------------------------------------
                    */

                    plugins: [{

                        id: 'customLabel',

                        afterDatasetsDraw(chart, args, pluginOptions) {

                            const { ctx } = chart;

                            chart.data.datasets.forEach((dataset, i) => {

                                const meta = chart.getDatasetMeta(i);

                                meta.data.forEach((bar, index) => {

                                    const data = dataset.data[index];

                                    ctx.save();

                                    ctx.fillStyle = '#111';

                                    ctx.font = 'bold 12px sans-serif';

                                    ctx.textAlign = 'center';

                                    ctx.fillText(
                                        data,
                                        bar.x,
                                        bar.y - 10
                                    );

                                    ctx.restore();

                                });

                            });

                        }

                    }]

                });

            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE FILTER BULAN
    |--------------------------------------------------------------------------
    */

    function toggleFilter()
    {
        let periode = $('#filterPeriode').val();

        if(periode === 'tahunan')
        {
            $('#filterBulan').closest('.col-md-2').hide();
        }
        else
        {
            $('#filterBulan').closest('.col-md-2').show();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT READY
    |--------------------------------------------------------------------------
    */

    $(document).ready(function(){

        toggleFilter();

        loadGrafik();

        /*
        |--------------------------------------------------------------------------
        | CHANGE FILTER
        |--------------------------------------------------------------------------
        */

        $('#filterPeriode').change(function(){

            toggleFilter();

            loadGrafik();

        });

        $('#filterKategori').change(function(){

            loadGrafik();

        });

        $('#filterTahun').change(function(){

            loadGrafik();

        });

        $('#filterBulan').change(function(){

            loadGrafik();

        });

        /*
        |--------------------------------------------------------------------------
        | BUTTON FILTER
        |--------------------------------------------------------------------------
        */

        $('#btnFilterGrafik').click(function(){

            loadGrafik();

        });

    });

    </script>

@endpush
