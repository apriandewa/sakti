@extends('frontend.main')

@section('container')

<main class="main">
<section class="section">
<div class="container">

@if(!empty($rekapGlobal))

<table class="table table-bordered text-center">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Perangkat Daerah</th>
            <th rowspan="2">Total ASN</th>

            @foreach($dates as $tgl)
                <th colspan="3">
                    {{ \Carbon\Carbon::parse($tgl)->format('d/m/Y') }}
                </th>
            @endforeach
        </tr>
        <tr>
            @foreach($dates as $tgl)
                <th>WFO</th>
                <th>WFH</th>
                <th>TDK</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($rekapGlobal as $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-start">{{ $data['nama'] }}</td>
                <td>{{ $data['total_asn'] }}</td>

                @foreach($dates as $tgl)
                    @php
                        $d = $data['tanggal'][$tgl];
                    @endphp
                    <td>{{ $d['wfo'] }}</td>
                    <td>{{ $d['wfh'] }}</td>
                    <td>{{ $d['tidak_hadir'] }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

@else
<div class="alert alert-warning text-center">
    Data tidak tersedia.
</div>
@endif

</div>
</section>
</main>

@endsection