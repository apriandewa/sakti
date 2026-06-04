@extends('frontend.main')

@section('container')

<main class="main">

<section class="section">

    <div class="container text-center mb-4">
        <h2>{{ $judul }}</h2>
        <p>{{ $subjudul }}</p>
    </div>

    <div class="container">

        @if(count($data['pegawai']) > 0)

            @foreach($data['pegawai'] as $pegawai)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <strong>{{ $pegawai['nama'] }}</strong> ({{ $pegawai['nip'] }})
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light text-center">
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
                                    <td class="text-center">{{ $item['jam_pagi'] ?? '-' }}</td>

                                    <td class="text-center">{{ $item['siang'] }}</td>
                                    <td class="text-center">{{ $item['jam_siang'] ?? '-' }}</td>

                                    <td class="text-center">{{ $item['sore'] }}</td>
                                    <td class="text-center">{{ $item['jam_sore'] ?? '-' }}</td>

                                    <td>
                                        @if($item['keterangan_waktu'] != '-')
                                            @foreach(explode('|', $item['keterangan_waktu']) as $ket)
                                                <div>
                                                    <span class="badge bg-info text-dark mb-1">
                                                        {{ trim($ket) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        @else
            <div class="alert alert-warning text-center">
                Data kantor tidak tersedia.
            </div>
        @endif

    </div>

</section>

</main>

@endsection