<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
    @page { margin: 0.5cm 0cm 2cm 0cm; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; line-height: 1.5; color: #000; }
    .container { padding-left: 2.5cm; padding-right: 2cm; }
    .kop-surat { text-align: center; margin-bottom: 3px; width: 100%; }
    .kop-surat img { width: 90%; height: auto; }
    .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 15px; }
    h3 { text-align: center; font-size: 14px; text-decoration: underline; }
    table.info td { padding: 2px 5px; vertical-align: top; }
    table.info td:first-child { width: 130px; font-weight: bold; }
    .section { margin: 15px 0; }
    .section h4 { font-size: 12px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    .content { text-align: justify; }
    .footer td { text-align: center; vertical-align: top; padding-top: 50px; }
</style>
</head><body>
<div class="kop-surat">
    @php
        $imagePath = public_path('eduadmin/images/kop-diskotik.png');
        $src = '';
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $src = 'data:image/png;base64,' . $imageData;
        }
    @endphp
    @if($src)
        <img src="{{ $src }}" alt="Kop Surat">
    @endif
</div>
<div class="container">
<div style="text-align: center; margin-bottom: 20px;">
    <h3 style="margin: 0; font-size: 16px; text-transform: uppercase; letter-spacing: 2px;">NOTULEN RAPAT</h3>
</div>

<table class="info">
    <tr><td>Nama Agenda</td><td>: {{ $data->nama }}</td></tr>
    <tr><td>Hari/Tanggal</td><td>: {{ $data->tanggal->translatedFormat('l, d F Y') }}</td></tr>
    <tr><td>Waktu</td><td>: {{ substr($data->jam_mulai,0,5) }} - {{ substr($data->jam_selesai,0,5) }} WIB</td></tr>
    <tr><td>Tempat</td><td>: {{ $data->tempat }}</td></tr>
    <tr><td>Pimpinan Rapat</td><td>: {{ $data->notulen->pimpinan_rapat }}</td></tr>
    <tr><td>Notulis</td><td>: {{ $data->notulen->notulis }}</td></tr>
    <tr><td>Jumlah Peserta</td><td>: {{ $data->peserta->count() }} orang</td></tr>
</table>

<div class="section">
    <h4>Jalannya Rapat</h4>
    <div class="content">{!! $data->notulen->isi_notulen !!}</div>
</div>

@if($data->notulen->hasil_rapat)
<div class="section">
    <h4>Kesimpulan Rapat</h4>
    <div class="content">{!! $data->notulen->hasil_rapat !!}</div>
</div>
@endif

@php
    $materi = $data->getfilesbyalias('rapat_materi');
@endphp
@if($materi->count() > 0)
<div class="section">
    <h4>Materi/Bahan Rapat</h4>
    <div class="content">
        <ul style="padding-left: 15px; margin: 0;">
            @foreach($materi as $mat)
            <li style="margin-bottom: 8px;">
                {{ $mat->name }} <br>
                <a href="{{ url($mat->link_download) }}" style="color: blue; text-decoration: underline; font-size: 10px;">Link Unduhan Materi</a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@php
    $isSignedByPimpinan = $data->getDokumenTteByJenis('notulen_pimpinan')?->status === 'signed';
    $logoPath = public_path('eduadmin/images/inhu.png');
    $logoSrc = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,' . $logoData;
    }
@endphp

<div class="footer" style="margin-top:40px;">
    <table width="100%"><tr>
        <td style="width: 50%; text-align: center; vertical-align: top;">
            Pimpinan Rapat,<br>
            @if(isset($pimpinan) && $pimpinan && $pimpinan->jabatanNama)
                {{ $pimpinan->jabatanNama->nama }},<br>
            @endif
            @if($data->jenis_tanda_tangan === 'elektronik')
                <div style="margin: 15px auto; width: 80px; border: 1px solid #ccc; padding: 4px;">
                    <div style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&ecc=H&data={{ urlencode(url('/verifikasi-undangan/' . $data->barcode_token . '?jenis=notulen')) }}" style="width: 100%; height: 100%; display: block;">
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" style="position: absolute; top: 28px; left: 28px; width: 14px; height: 14px; background: #ffffff; padding: 1px; border-radius: 2px;">
                        @endif
                    </div>
                    <div style="font-size:8px; text-align:center; margin-top:2px;">TTE Valid</div>
                </div>
            @else
                <br><br><br><br>
            @endif
            <strong><u>{{ $data->notulen->pimpinan_rapat }}</u></strong>
            @if(isset($pimpinan) && $pimpinan)
                @if($pimpinan->nip)
                    <br>NIP. {{ $pimpinan->nip }}
                @endif
            @endif
        </td>
        <td style="width: 50%; text-align: center; vertical-align: top;">
            Notulis,<br>
            @if(isset($notulis) && $notulis && $notulis->jabatanNama)
                {{ $notulis->jabatanNama->nama }},<br>
            @endif
            @if($data->jenis_tanda_tangan === 'elektronik')
                <div style="margin: 15px auto; width: 80px; border: 1px solid #ccc; padding: 4px;">
                    <div style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&ecc=H&data={{ urlencode(url('/verifikasi-undangan/' . $data->barcode_token . '?jenis=notulen')) }}" style="width: 100%; height: 100%; display: block;">
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" style="position: absolute; top: 28px; left: 28px; width: 14px; height: 14px; background: #ffffff; padding: 1px; border-radius: 2px;">
                        @endif
                    </div>
                    <div style="font-size:8px; text-align:center; margin-top:2px;">TTE Valid</div>
                </div>
            @else
                <br><br><br><br>
            @endif
            <strong><u>{{ $data->notulen->notulis }}</u></strong>
            @if(isset($notulis) && $notulis)
                @if($notulis->nip)
                    <br>NIP. {{ $notulis->nip }}
                @endif
            @endif
        </td>
    </tr></table>
</div>
</div>

@php
    $dokumentasi = $data->getfilesbyalias('rapat_dokumentasi');
@endphp
@if($dokumentasi->count() > 0)
<div style="page-break-before: always;"></div>
<div class="container">
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;">LAMPIRAN FOTO KEGIATAN</h3>
    </div>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        @foreach($dokumentasi->chunk(2) as $chunk)
        <tr>
            @foreach($chunk as $foto)
                @php
                    $disk = $foto->data['disk'] ?? 'public';
                    $target = $foto->data['target'] ?? '';
                    $path = Storage::disk($disk)->path($target);
                    $imgSrc = '';
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $dataImg = file_get_contents($path);
                        $imgSrc = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
                    } else {
                        $imgSrc = url($foto->link_stream); // fallback
                    }
                @endphp
                <td style="width: 50%; padding: 10px; vertical-align: top; text-align: center;">
                    <img src="{{ $imgSrc }}" style="max-width: 100%; max-height: 350px; border: 1px solid #ddd; padding: 5px;">
                    <div style="margin-top: 5px; font-size: 10pt; color: #555;">{{ $foto->name }}</div>
                </td>
            @endforeach
            @if($chunk->count() == 1)
                <td style="width: 50%; padding: 10px;"></td>
            @endif
        </tr>
        @endforeach
    </table>
</div>
@endif

</body></html>
