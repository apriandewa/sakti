@extends('frontend.main')

@section('container')

<main class="main">

<section class="services section">

    <div class="container section-title text-center">
        <h2>{{ $judul }}</h2>
        <p>{{ $subjudul }}</p>
    </div>

    <div class="container">

        {{-- 🔥 INFO PEGAWAI + FOTO --}}
        <div class="card mb-4">
            <div class="card-body d-flex align-items-center gap-3">

                {{-- FOTO --}}
                @if(!empty($pegawai['foto']))
                    <img 
                        src="data:image/jpeg;base64,{{ $pegawai['foto'] }}"
                        style="width:80px; height:80px; object-fit:cover; border-radius:50%; border:3px solid #0d6efd;"
                    >
                @else
                    <img 
                        src="https://via.placeholder.com/80"
                        style="border-radius:50%;"
                    >
                @endif

                <div>
                    <h5 class="mb-1">Nama: {{ $pegawai['nama'] }}</h5>
                    <p class="mb-0">NIP: {{ $pegawai['nip'] }}</p>
                </div>

            </div>
        </div>

        {{-- 🔥 DATA PRESENSI --}}
        @if(count($pegawai['presensi']) > 0)

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-primary text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>Pagi</th>
                        <th>Jam</th>
                        <th>Siang</th>
                        <th>Jam</th>
                        <th>Sore</th>
                        <th>Jam</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pegawai['presensi'] as $item)
                    <tr>
                        <td>{{ $item['tgl'] }}</td>

                        <td class="text-center">{{ $item['pagi'] }}</td>
                        <td>{{ $item['jam_pagi'] ?? '-' }}</td>

                        <td class="text-center">{{ $item['siang'] }}</td>
                        <td>{{ $item['jam_siang'] ?? '-' }}</td>

                        <td class="text-center">{{ $item['sore'] }}</td>
                        <td>{{ $item['jam_sore'] ?? '-' }}</td>

                        <td class="text-center">
                            <span class="badge 
                                @if($item['keterangan'] == 'TK') bg-danger
                                @elseif($item['keterangan'] == 'HN') bg-success
                                @elseif($item['keterangan'] == 'PC') bg-warning text-dark
                                @else bg-secondary
                                @endif
                            ">
                                {{ $item['keterangan'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
        <div class="alert alert-warning text-center">
            Data presensi tidak tersedia.
        </div>
        @endif

    </div>

</section>

</main>

@endsection