<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0.5cm 0cm 2cm 0cm; /* Set left/right margin to 0 for full width kop */
        }
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 11pt; 
            line-height: 1.5; 
            color: #000; 
        }
        .kop-surat {
            text-align: center; 
            margin-bottom: 2px;
            width: 100%;
        }
        .kop-surat img {
            width: 88%; 
            height: auto;
        }
        .container {
            padding-left: 2.5cm;
            padding-right: 2cm;
        }
        .tgl-surat {
            text-align: right; 
            margin-bottom: 6px;
        }
        .meta-surat {
            width: 100%; 
            margin-bottom: 8px;
        }
        .meta-surat td {
            vertical-align: top;
        }
        .content { 
            text-align: justify; 
            margin-bottom: 8px;
        }
        .content p {
            margin-top: 0;
            margin-bottom: 8px;
        }
        table.info-kegiatan { 
            width: 90%; 
            margin: 12px auto; 
            border-collapse: collapse;
        }
        table.info-kegiatan td { 
            vertical-align: top; 
            padding: 4px 0;
        }
        table.info-kegiatan td:first-child { 
            width: 130px; 
        }
        table.info-kegiatan td:nth-child(2) { 
            width: 15px; 
            text-align: center;
        }
        .footer-ttd {
            width: 100%;
            margin-top: 12px;
        }
        .ttd-box {
            float: right;
            width: 300px;
            text-align: left;
        }
        .qr-box {
            margin-top: 20px;
            text-align: left;
            font-size: 9pt;
            border-top: 1px dashed #ccc;
            padding-top: 12px;
        }
    </style>
</head>
<body>

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
    <div class="tgl-surat">
        Pematang Reba, {{ \Carbon\Carbon::parse($data->created_at ?? now())->locale('id')->translatedFormat('d F Y') }}
    </div>

    <table class="meta-surat">
        <tr>
            <td style="width: 10%;">Nomor</td>
            <td style="width: 2%;">:</td>
            <td style="width: 48%;">{{ $data->surat_nomor ?? '-' }}</td>
        </tr>
        <tr>
            <td>Sifat</td>
            <td>:</td>
            <td>{{ $data->surat_sifat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>{{ $data->surat_lampiran ?? '-' }}</td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>:</td>
            <td>{{ $data->surat_hal ?? '-' }}</td>
        </tr>
    </table>
    
    <table style="margin-bottom: 20px;">
        <tr>     
            <td>
                Kepada Yth.<br>
                @if(($data->jenis_tujuan_surat ?? 'tunggal') === 'lampiran')
                    <strong>Daftar terlampir</strong><br>
                @else
                    <strong>{!! $data->surat_tujuan ? nl2br(e($data->surat_tujuan)) : '________________________' !!}</strong><br>
                @endif
                di - <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Tempat
            </td>
        </tr>
    </table>

    <div class="content">
        <p>Dengan hormat,</p>
        
        @if($data->dasar_dari)
        <p>
            Berdasarkan surat dari {{ $data->dasar_dari }} Nomor {{ $data->dasar_no }} Tanggal {{ $data->dasar_tgl ? \Carbon\Carbon::parse($data->dasar_tgl)->locale('id')->translatedFormat('d F Y') : '-' }} Perihal {{ $data->dasar_hal }}.
        </p>
        @endif

        @if($data->deskripsi)
        <div style="margin-top: 15px; margin-bottom: 15px;">
            {!! $data->deskripsi !!}
        </div>
        @endif

        <table class="info-kegiatan">
            <tr>
                <td>Hari, Tanggal</td>
                <td>:</td>
                <td><strong>{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong></td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ substr($data->jam_mulai, 0, 5) }} s.d. {{ substr($data->jam_selesai, 0, 5) }} WIB</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>
                    @if(($data->tipe_rapat ?? 'offline') === 'online')
                        <strong>Online via Zoom Meeting</strong><br>
                        Meeting ID: {{ $data->zoom_meeting_id }}<br>
                        Passcode: {{ $data->zoom_password }}
                    @else
                        <strong>{{ $data->tempat }}</strong>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Acara</td>
                <td>:</td>
                <td>{{ $data->acara }}</td>
            </tr>
        </table>

        <p style="margin-top: 15px;">
            Mengingat pentingnya acara tersebut, kami mohon kehadiran Bapak/Ibu/Saudara/i tepat pada waktunya. Demikian undangan ini disampaikan, atas perhatian dan kehadirannya diucapkan terima kasih.
        </p>

        @if($data->catatan)
        <div style="margin-top: 15px; font-size: 10.5pt; font-style: italic; line-height: 1.4;">
            <strong>Catatan:</strong><br>
            {!! nl2br(e($data->catatan)) !!}
        </div>
        @endif
    </div>

    <div class="footer-ttd">
        <div class="ttd-box">
            @if($data->pegawai)
                <div style="margin-bottom: 8px;">
                    {{ $data->pegawai->jabatanNama ? $data->pegawai->jabatanNama->nama : 'Jabatan' }},
                </div>
                @if(($data->jenis_tanda_tangan ?? 'manual') === 'elektronik')
                    <div style="margin: 8px 0; height: 80px; text-align: left;">
                        <div style="position: relative; width: 80px; height: 80px;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&ecc=H&data={{ urlencode(url('/verifikasi-undangan/' . $data->barcode_token . '?jenis=undangan')) }}" alt="QR TTE" style="width: 80px; height: 80px; display: block;">
                            @php
                                $logoPath = public_path('eduadmin/images/inhu.png');
                                $logoSrc = '';
                                if (file_exists($logoPath)) {
                                    $logoData = base64_encode(file_get_contents($logoPath));
                                    $logoSrc = 'data:image/png;base64,' . $logoData;
                                }
                            @endphp
                            @if($logoSrc)
                                <img src="{{ $logoSrc }}" style="position: absolute; top: 32px; left: 32px; width: 16px; height: 16px; background: #ffffff; padding: 1px; border-radius: 2px;">
                            @endif
                        </div>
                    </div>
                @else
                    <div style="margin-top: 15px; margin-bottom: 15px;">
                        <br><br><br>
                    </div>
                @endif
                <div style="margin-top: 8px; line-height: 1.4;">
                    <strong><u>{{ strtoupper($data->pegawai->nama ?? '________________________') }}</u></strong>
                    @if($data->pegawai->pangkat)
                        <br>{{ $data->pegawai->pangkat->nama }}
                    @endif
                    @if($data->pegawai->nip)
                        <br>NIP. {{ $data->pegawai->nip }}
                    @endif
                </div>
            @else
                <div style="margin-bottom: 8px;">
                    Pembuat Agenda,
                </div>
                <div style="margin-top: 15px; margin-bottom: 15px;">
                    <br><br><br>
                </div>
                <div style="margin-top: 8px; line-height: 1.4;">
                    <strong><u>{{ strtoupper($data->user->name ?? '________________________') }}</u></strong>
                </div>
            @endif
        </div>
        <div style="clear: both;"></div>
    </div>

    <div class="qr-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 90px; vertical-align: middle;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($data->absensi_url) }}" alt="QR" style="width: 75px; height: 75px;">
                </td>
                <td style="vertical-align: middle; color: #333;">
                    <strong>Silahkan Isi Daftar Hadir Online:</strong><br>
                    Scan QR Code di samping menggunakan kamera smartphone Anda atau kunjungi tautan:<br>
                    <a href="{{ $data->absensi_url }}" style="color: #0369a1; text-decoration: none;">{{ $data->absensi_url }}</a>
                </td>
            </tr>
        </table>
    </div>

</div>

@if(($data->jenis_tujuan_surat ?? 'tunggal') === 'lampiran' && !empty($data->surat_tujuan_lampiran))
    @php
        $recipients = array_filter(array_map('trim', explode("\n", $data->surat_tujuan_lampiran)));
    @endphp
    @if(count($recipients) > 0)
        <div style="page-break-before: always;"></div>
        <div class="container" style="margin-top: 1cm;">
            <table style="width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; font-size: 10pt;">
                <tr>
                    <td style="width: 18%;">Lampiran Surat</td>
                    <td style="width: 2%;">:</td>
                    <td>Undangan Rapat</td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>{{ $data->surat_nomor ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->locale('id')->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>:</td>
                    <td>{{ $data->surat_hal ?? '-' }}</td>
                </tr>
            </table>

            <h4 style="text-align: center; margin-top: 30px; margin-bottom: 20px; text-decoration: underline;">DAFTAR PENERIMA UNDANGAN</h4>

            <ol style="padding-left: 20px; line-height: 1.8;">
                @foreach($recipients as $recipient)
                    <li>{{ $recipient }}</li>
                @endforeach
            </ol>
        </div>
    @endif
@endif

</body>
</html>
