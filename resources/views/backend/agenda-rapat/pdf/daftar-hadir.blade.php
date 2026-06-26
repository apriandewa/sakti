<!DOCTYPE html>
<html><head><meta charset="utf-8">
<style>
    @page { margin: 0.5cm 0cm 2cm 0cm; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11pt; line-height: 1.5; color: #000; }
    .container { padding-left: 2.5cm; padding-right: 2cm; }
    .kop-surat { text-align: center; margin-bottom: 3px; width: 100%; }
    .kop-surat img { width: 100%; height: auto; }
    .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 15px; }
    h3 { text-align: center; font-size: 14px; text-decoration: underline; margin: 20px 0 10px; }
    table.info td { padding: 2px 5px; vertical-align: top; }
    table.info td:first-child { width: 130px; font-weight: bold; }
    table.peserta { width: 100%; border-collapse: collapse; margin: 10px 0; }
    table.peserta th, table.peserta td { border: 1px solid #333; padding: 5px 8px; text-align: center; }
    table.peserta th { background: #f0f0f0; font-size: 10px; }
    table.peserta td { font-size: 10px; }
    .ttd-img { max-height: 35px; }
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
    <h2 style="margin: 0; font-size: 16px; text-transform: uppercase; letter-spacing: 2px;">DAFTAR HADIR RAPAT</h2>
</div>

<table class="info">
    <tr><td>Nama Agenda</td><td>: {{ $data->nama }}</td></tr>
    <tr><td>Hari/Tanggal</td><td>: {{ $data->tanggal->translatedFormat('l, d F Y') }}</td></tr>
    <tr><td>Waktu</td><td>: {{ substr($data->jam_mulai,0,5) }} - {{ substr($data->jam_selesai,0,5) }} WIB</td></tr>
    <tr><td>Tempat</td><td>: {{ $data->tempat }}</td></tr>
    <tr><td>Jumlah Peserta</td><td>: {{ $data->peserta->count() }} orang</td></tr>
</table>

<table class="peserta">
    <thead><tr><th>No</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Instansi</th><th>No. HP</th><th>Tanda Tangan</th></tr></thead>
    <tbody>
    @forelse($data->peserta as $i => $p)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td style="text-align:left;">{{ $p->nama }}</td>
        <td>{{ $p->nip ?? '-' }}</td>
        <td>{{ $p->jabatan ?? '-' }}</td>
        <td>{{ $p->instansi ?? '-' }}</td>
        <td>{{ $p->no_hp ?? '-' }}</td>
        <td>@if($p->tanda_tangan)<img src="{{ $p->tanda_tangan }}" class="ttd-img">@else - @endif</td>
    </tr>
    @empty
    <tr><td colspan="7">Belum ada peserta</td></tr>
    @endforelse
    </tbody>
</table>

<div style="width: 100%; margin-top: 20px; page-break-inside: avoid;">
    <div style="float: right; width: 300px; text-align: center;">
        Pimpinan Rapat<br>
        @if($data->pegawai)
            {{ $data->pegawai->jabatanNama ? $data->pegawai->jabatanNama->nama : 'Jabatan' }}<br>
            @if(($data->jenis_tanda_tangan ?? 'manual') === 'elektronik')
                <div style="margin: 6px auto; text-align: center;">
                    <div style="position: relative; width: 75px; height: 75px; margin: 0 auto 5px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&ecc=H&data={{ urlencode(url('/verifikasi-undangan/' . $data->barcode_token . '?jenis=daftar_hadir')) }}" alt="QR TTE" style="width: 100%; height: 100%; display: block;">
                        @php
                            $logoPath = public_path('eduadmin/images/inhu.png');
                            $logoSrc = '';
                            if (file_exists($logoPath)) {
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoSrc = 'data:image/png;base64,' . $logoData;
                            }
                        @endphp
                        @if($logoSrc)
                            <img src="{{ $logoSrc }}" style="position: absolute; top: 30px; left: 30px; width: 15px; height: 15px; background: #ffffff; padding: 1px; border-radius: 2px;">
                        @endif
                    </div>
                    <span style="font-size: 7.5pt; color: #047857; font-family: monospace;">Ditandatangani secara elektronik</span>
                </div>
            @else
                <br><br><br><br>
            @endif
            <strong><u>{{ strtoupper($data->pegawai->nama ?? '________________________') }}</u></strong>
            @if($data->pegawai->pangkat)
                <br>{{ $data->pegawai->pangkat->nama }}
            @endif
            @if($data->pegawai->nip)
                <br>NIP. {{ $data->pegawai->nip }}
            @endif
        @else
            <br><br><br><br>
            <strong><u>________________________</u></strong>
        @endif
    </div>
    <div style="clear: both;"></div>
</div>

</div>
</body></html>
