<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span()->text("Nama")->class("control-label") !!}
                    {!! html()->p($data->nama)->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("NIP")->class("control-label") !!}
                    {!! html()->p($data->nip)->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("Jabatan")->class("control-label") !!}
                    {!! html()->p($data->skp_jabatan ?? '-')->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("Unit Kerja")->class("control-label") !!}
                    {!! html()->p($data->skp_unor ?? '-')->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("Golongan Ruang")->class("control-label") !!}
                    {!! html()->p($data->golru ?? '-')->class("form-control") !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span()->text("Periode SKP")->class("control-label") !!}
                    {!! html()->p(
                        optional($data->periode_awal_skp)->format('d-m-Y') . ' s/d ' . optional($data->periode_akhir_skp)->format('d-m-Y')
                    )->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("Hasil Kerja")->class("control-label") !!}
                    {!! html()->p(strtoupper($data->hasil_kerja ?? '-'))->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("Perilaku Kerja")->class("control-label") !!}
                    {!! html()->p(strtoupper($data->perilaku_kerja ?? '-'))->class("form-control") !!}
                </div>
                <div class="form-group">
                    {!! html()->span()->text("Hasil Akhir")->class("control-label") !!}
                    {!! html()->p(strtoupper($data->hasil_akhir ?? '-'))->class("form-control") !!}
                </div>
            </div>

            <div class="col-md-12">
                <hr>
                <div class="form-group">
                    {!! html()->span()->text("Pejabat Penilai")->class("control-label") !!}
                    {!! html()->p(($data->pegawai_atasan_nama ?? '-') . ' — ' . ($data->pegawai_atasan_jabatan ?? '-'))->class("form-control") !!}
                </div>
                @if($data->waktu_dinilai)
                    <div class="form-group">
                        {!! html()->span()->text("Waktu Dinilai")->class("control-label") !!}
                        {!! html()->p($data->waktu_dinilai->format('d-m-Y H:i'))->class("form-control") !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-eye"></i> Detail Penilaian e-Kinerja — {{ $data->nama }}');
</script>