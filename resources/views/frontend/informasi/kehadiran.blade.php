@extends('frontend.main')

@section('container')

<main class="main">

<section class="section">

    <div class="container text-center mb-4">
        <h2>{{ $judul }}</h2>
        <p>{{ $subjudul }}</p>
    </div>

    <div class="container">

        {{-- 🔥 TOTAL --}}
        <div class="mb-4 text-center">
            <span class="badge bg-danger fs-6">Total TK: {{ $data['total']['TK'] }}</span>
            <span class="badge bg-success fs-6">Total HN: {{ $data['total']['HN'] }}</span>
            <span class="badge bg-warning text-dark fs-6">Total PC: {{ $data['total']['PC'] }}</span>
        </div>

        {{-- 🔥 TABEL --}}
        @if(count($data['pegawai']) > 0)

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>TK</th>
                        <th>HN</th>
                        <th>PC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['pegawai'] as $i => $pegawai)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $pegawai['nama'] }}</td>
                        <td>{{ $pegawai['nip'] }}</td>

                        <td class="text-center text-danger">
                            {{ $pegawai['statistik']['TK'] }}
                        </td>

                        <td class="text-center text-success">
                            {{ $pegawai['statistik']['HN'] }}
                        </td>

                        <td class="text-center text-warning">
                            {{ $pegawai['statistik']['PC'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else
        <div class="alert alert-warning text-center">
            Data statistik tidak tersedia.
        </div>
        @endif

    </div>

</section>

</main>

@endsection