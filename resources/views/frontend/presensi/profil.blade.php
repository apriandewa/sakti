@extends('frontend.main')

@section('container')

<div class="container mt-4 text-center">

@if($data['foto'])
<img src="data:image/jpeg;base64,{{ $data['foto'] }}" 
     style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
@endif

<h4 class="mt-2">{{ $data['nama'] }}</h4>
<p>{{ $data['nip'] }}</p>

<div class="table-responsive">
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['presensi'] as $item)
        <tr>
            <td>{{ $item['tgl'] }}</td>
            <td>{{ $item['keterangan'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

</div>

@endsection