@extends('frontend.main')

@section('container')

<div class="container mt-4">

<h3 class="text-center mb-4">
    Rekap Bulanan Pegawai - {{ now()->translatedFormat('F Y') }}
</h3>

{{-- 🔥 KALENDER --}}
<div class="text-center mb-3">
    <span class="badge bg-primary">Hari Kerja: {{ $kalender['hari_kerja'] }}</span>
    <span class="badge bg-success">Weekend: {{ $kalender['weekend'] }}</span>
    <span class="badge bg-danger">Libur Nasional: {{ $kalender['libur'] }}</span>
</div>

{{-- 🔥 CHART --}}

<div class="row">
    <div class="col-6">
        <h5 class="mb-2">Statistik Kehadiran Hari Ini</h5>

            <div class="mb-3">
                <small>Jumlah Pegawai</small>
                <div class="progress mb-2">
                    <div class="progress-bar bg-primary" style="width:100%">
                        {{ $statToday['pegawai'] }}
                    </div>
                </div>

                <small>Hadir</small>
                <div class="progress mb-2">
                    <div class="progress-bar bg-success"
                        style="width: {{ $statToday['pegawai'] ? ($statToday['hadir']/$statToday['pegawai']*100) : 0 }}%">
                        {{ $statToday['hadir'] }}
                    </div>
                </div>

                <small>Tidak Hadir</small>
                <div class="progress mb-2">
                    <div class="progress-bar bg-danger"
                        style="width: {{ $statToday['pegawai'] ? ($statToday['tidak_hadir']/$statToday['pegawai']*100) : 0 }}%">
                        {{ $statToday['tidak_hadir'] }}
                    </div>
                </div>

                <small>Terlambat</small>
                <div class="progress mb-2">
                    <div class="progress-bar bg-warning"
                        style="width: {{ $statToday['pegawai'] ? ($statToday['terlambat']/$statToday['pegawai']*100) : 0 }}%">
                        {{ $statToday['terlambat'] }}
                    </div>
                </div>

            </div>
    </div>
    
    <div class="col-6">
        <h5 class="mb-2">Statistik Kehadiran Bulanan</h5>

        <div class="mb-3">

            <small>Total Kehadiran (Hari Kerja)</small>
            <div class="progress mb-2">
                <div class="progress-bar bg-primary"
                    style="width:100%">
                    {{ $statMonth['kehadiran_total'] }}
                </div>
            </div>

            <small>Hadir</small>
            <div class="progress mb-2">
                <div class="progress-bar bg-success"
                    style="width: {{ $statMonth['kehadiran_total'] ? ($statMonth['hadir']/$statMonth['kehadiran_total']*100) : 0 }}%">
                    {{ $statMonth['hadir'] }}
                </div>
            </div>

            <small>Tidak Hadir</small>
            <div class="progress mb-2">
                <div class="progress-bar bg-danger"
                    style="width: {{ $statMonth['kehadiran_total'] ? ($statMonth['tidak_hadir']/$statMonth['kehadiran_total']*100) : 0 }}%">
                    {{ $statMonth['tidak_hadir'] }}
                </div>
            </div>

            <small>Terlambat</small>
            <div class="progress mb-2">
                <div class="progress-bar bg-warning"
                    style="width: {{ $statMonth['kehadiran_total'] ? ($statMonth['terlambat']/$statMonth['kehadiran_total']*100) : 0 }}%">
                    {{ $statMonth['terlambat'] }}
                </div>
            </div>

        </div>
    </div>
</div>




{{-- 🔥 RANKING --}}

<h4 class="text-center mb-4">
    Rekapitulasi Kehadiran Bulan : {{ now()->translatedFormat('F Y') }}
</h4>

<div class="table-responsive">
<table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>Hadir</th>
            <th>Tidak Hadir</th>
            <th>Telat Masuk</th>
            <th>Pulang Cepat</th>
            <th>Hari Kerja</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
        @foreach($pegawaiList as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>

            <td class="text-start">
                <a href="{{ url('presensi/detail/'.$p['nip']) }}">
                    {{ $p['nama'] }}
                </a>
            </td>

            <td>{{ $p['nip'] }}</td>

            <td class="text-success">{{ $p['summary']['hadir'] ?? 0 }}</td>
            <td class="text-danger">{{ $p['summary']['tk'] ?? 0 }}</td>
            <td class="text-warning">{{ $p['summary']['telat'] ?? 0 }}</td>
            <td class="text-warning">{{ $p['summary']['pulang_cepat'] ?? 0 }}</td>
            <td>{{ $p['summary']['hari_kerja'] ?? 0 }}</td>

            <td>
                <a href="{{ url('presensi/detail/'.$p['nip']) }}" class="btn btn-sm btn-primary">
                    Detail
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

</div>



@endsection