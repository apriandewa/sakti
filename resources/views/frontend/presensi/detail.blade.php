@extends('frontend.main')

@section('container')

<div class="container mt-4">

    {{-- 🔥 HEADER --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header text-center fw-bold">
            Rekap Absen Pegawai Bulan : {{ now()->translatedFormat('F Y') }}
        </div>

        <div class="card-body">
            <div class="row align-items-center">

                {{-- ========================= --}}
                {{-- 🔵 PROFIL (KIRI) --}}
                {{-- ========================= --}}
                <div class="col-md-6 text-center">

                    {{-- FOTO BESAR --}}
                    @if(!empty($pegawai['foto']))
                        <img src="data:image/jpeg;base64,{{ $pegawai['foto'] }}" 
                            class="border shadow-sm mb-3"
                            style="width:140px;height:140px;object-fit:cover;">
                    @else
                        <i class="bi bi-person-circle text-muted mb-3" style="font-size: 120px;"></i>
                    @endif

                    {{-- NAMA & NIP --}}
                    <h4 class="mb-1 fw-bold">{{ $pegawai['nama'] ?? '-' }}</h4>
                    <p class="text-muted mb-0">NIP: {{ $pegawai['nip'] ?? '-' }}</p>

                    <span class="badge bg-dark">
                        Total Potongan: {{ $pegawai['total_potongan'] ?? 0 }}
                    </span>
                </div>

                {{-- ========================= --}}
                {{-- 🟢 PROGRESS STATISTIK --}}
                {{-- ========================= --}}
                <div class="col-md-6 mt-4 mt-md-0">

                    @php
                        $hariKerja = $pegawai['summary']['hari_kerja'] ?? 0;
                        $hadir = $pegawai['summary']['hadir'] ?? 0;
                        $telat = $pegawai['summary']['telat'] ?? 0;
                        $tk = $pegawai['summary']['tk'] ?? 0;
                        $pulangCepat = $pegawai['summary']['pulang_cepat'] ?? 0;

                        $persenHadir = $hariKerja > 0 ? round(($hadir / $hariKerja) * 100) : 0;
                        $persenTelat = $hariKerja > 0 ? round(($telat / $hariKerja) * 100) : 0;
                        $persenTK = $hariKerja > 0 ? round(($tk / $hariKerja) * 100) : 0;
                        $persenPC = $hariKerja > 0 ? round(($pulangCepat / $hariKerja) * 100) : 0;
                    @endphp

                    {{-- 🔵 HARI KERJA --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Hari Kerja</small>
                            <small>{{ $hariKerja }}</small>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-info"
                                style="width: 100%">
                                {{ $hariKerja }}
                            </div>
                        </div>
                    </div>

                    {{-- 🟢 HADIR --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Hadir</small>
                            <small>{{ $hadir }} / {{ $hariKerja }} ({{ $persenHadir }}%)</small>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success"
                                style="width: {{ $persenHadir }}%">
                                {{ $persenHadir }}%
                            </div>
                        </div>
                    </div>

                    {{-- 🔴 TIDAK HADIR --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Tidak Hadir</small>
                            <small>{{ $tk }} ({{ $persenTK }}%)</small>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-danger"
                                style="width: {{ $persenTK }}%">
                                {{ $persenTK }}%
                            </div>
                        </div>
                    </div>

                    {{-- 🟡 TELAT --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <small>Telat Masuk</small>
                            <small>{{ $telat }} ({{ $persenTelat }}%)</small>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-warning text-dark"
                                style="width: {{ $persenTelat }}%">
                                {{ $persenTelat }}%
                            </div>
                        </div>
                    </div>

                    {{-- ⚪ PULANG CEPAT --}}
                    <div>
                        <div class="d-flex justify-content-between">
                            <small>Pulang Cepat</small>
                            <small>{{ $pulangCepat }} ({{ $persenPC }}%)</small>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-secondary"
                                style="width: {{ $persenPC }}%">
                                {{ $persenPC }}%
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- 🔥 TABEL --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-bordered text-center">

                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th style="min-width:180px">Tanggal</th>
                        <th>Pagi</th>
                        <th>Jam</th>
                        <th>Siang</th>
                        <th>Jam</th>
                        <th>Sore</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th style="min-width:200px">Keterangan</th>
                        <th>Potongan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pegawai['presensi'] as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        
                        {{-- TANGGAL INDONESIA --}}
                        <td class="text-start">
                            {{ $item['tgl_indonesia'] ?? '-' }}
                        </td>

                        {{-- PAGI --}}
                        <td>{{ $item['pagi'] ?? '-' }}</td>
                        <td>{{ $item['jam_pagi'] ?? '-' }}</td>

                        {{-- SIANG --}}
                        <td>{{ $item['siang'] ?? '-' }}</td>
                        <td>{{ $item['jam_siang'] ?? '-' }}</td>

                        {{-- SORE --}}
                        <td>{{ $item['sore'] ?? '-' }}</td>
                        <td>{{ $item['jam_sore'] ?? '-' }}</td>

                        {{-- STATUS --}}
                        <td>
                            <span class="badge bg-{{ $item['status_class'] ?? 'secondary' }}">
                                {{ $item['status_label'] ?? '-' }}
                            </span>
                        </td>

                        {{-- KETERANGAN --}}
                        <td class="text-start">
                            {{ $item['keterangan_waktu'] ?? '-' }}
                        </td>

                        {{-- POTONGAN --}}
                        <td>
                            @if($item['potongan'] > 0)
                                <span class="badge bg-danger">
                                    {{ $item['potongan'] }}
                                </span>
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- 🔥 BACK BUTTON --}}
    <div class="text-center mt-3">
        <a href="{{ url('presensi') }}" class="btn btn-outline-primary">
            ← Kembali ke Rekap
        </a>
    </div>
</div>

@endsection