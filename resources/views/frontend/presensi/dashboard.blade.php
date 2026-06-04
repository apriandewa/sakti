@extends('frontend.main')

@section('container')

<div class="container mt-4">

<h3 class="text-center mb-3">Dashboard Presensi Pegawai</h3>

{{-- 🔥 KALENDER --}}
<div class="text-center mb-3">
    <span class="badge bg-primary">Hari Kerja: {{ $kalender['hari_kerja'] }}</span>
    <span class="badge bg-success">Weekend: {{ $kalender['weekend'] }}</span>
    <span class="badge bg-danger">Libur Nasional: {{ $kalender['libur'] }}</span>
</div>

{{-- 🔥 CHART --}}
<canvas id="chartPresensi" height="100"></canvas>

<hr>

{{-- 🔥 RANKING --}}
<h5 class="mb-3">Ranking Pegawai Disiplin</h5>

<div class="table-responsive">
<table class="table table-bordered text-center">
    <thead class="table-dark">
        <tr>
            <th>Rank</th>
            <th>Nama</th>
            <th>NIP</th>
            <th>Hadir</th>
            <th>Telat</th>
            <th>Pulang Cepat</th>
            <th>TK</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pegawaiList as $i => $p)
        <tr>
            <td><strong>{{ $i+1 }}</strong></td>
            <td class="text-start">
                <a href="{{ route('presensi.detail', $p['nip']) }}" 
                   class="fw-bold text-decoration-none">
                    {{ $p['nama'] }}
                </a>
            </td>
            <td>{{ $p['nip'] }}</td>
            <td class="text-success">{{ $p['statistik']['HN'] }}</td>
            <td class="text-warning">{{ $p['statistik']['TMM'] }}</td>
            <td class="text-warning">{{ $p['statistik']['PC'] }}</td>
            <td class="text-danger">{{ $p['statistik']['TK'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

</div>

{{-- 🔥 CHART JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chartPresensi');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Hadir', 'Telat', 'Pulang Cepat', 'TK'],
        datasets: [{
            label: 'Statistik Kehadiran',
            data: [
                {{ $chart['HN'] }},
                {{ $chart['TMM'] }},
                {{ $chart['PCM'] }},
                {{ $chart['TK'] }}
            ]
        }]
    },
});
</script>

@endsection