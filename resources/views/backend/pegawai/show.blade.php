@php
    $fotoFile = $data->getfilebyalias('foto_pegawai');
    $tteFile = $data->getfilebyalias('spesimen_tte');
@endphp

<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-4 text-center border-right">
                <div class="mb-3">
                    @if($fotoFile && $fotoFile->exists)
                        <img src="{{ $fotoFile->link_stream }}" alt="Foto Pegawai" class="img-thumbnail rounded" style="max-height: 250px; object-fit: cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light border rounded" style="height: 250px; width: 100%;">
                            <span class="text-muted"><i class="fa fa-user fa-5x"></i><br>Tidak Ada Foto</span>
                        </div>
                    @endif
                </div>
                <div class="mt-3">
                    <h6>Spesimen TTE:</h6>
                    @if($tteFile && $tteFile->exists)
                        <img src="{{ $tteFile->link_stream }}" alt="Spesimen TTE" class="img-thumbnail rounded" style="max-height: 100px; object-fit: contain;">
                    @else
                        <span class="text-muted"><i class="fa fa-pencil-square-o"></i> Belum Ada Spesimen</span>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
                <table class="table table-striped">
                    <tr>
                        <th width="35%">Nama Lengkap</th>
                        <td>
                            @php
                                $dep = $data->gelar_depan ? $data->gelar_depan . ' ' : '';
                                $bel = $data->gelar_belakang ? ', ' . $data->gelar_belakang : '';
                            @endphp
                            <strong>{{ $dep . $data->nama . $bel }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>NIP</th>
                        <td>{{ $data->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td>{{ $data->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Kerja</th>
                        <td>{{ $data->statusPegawai ? $data->statusPegawai->nama : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Pangkat / Golongan</th>
                        <td>{{ $data->pangkat ? $data->pangkat->nama : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Jabatan</th>
                        <td>{{ $data->jabatanJenis ? $data->jabatanJenis->nama : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Jabatan</th>
                        <td>{{ $data->jabatanNama ? $data->jabatanNama->nama : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Bidang</th>
                        <td>{{ $data->bidang ? $data->bidang->nama : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td>{{ $data->jenis_kelamin }}</td>
                    </tr>
                    <tr>
                        <th>Agama</th>
                        <td>{{ $data->agama }}</td>
                    </tr>
                    <tr>
                        <th>Pendidikan Terakhir</th>
                        <td>{{ $data->pendidikan_terakhir }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>{{ $data->telpon }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $data->alamat }}</td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>{{ $data->periode }}</td>
                    </tr>
                    <tr>
                        <th>Status Aktif</th>
                        <td>
                            @if($data->status == 'aktif')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Akun Login Terhubung</th>
                        <td>{{ $data->user ? $data->user->name . ' (' . $data->user->email . ')' : 'Belum dihubungkan ke akun login' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $('.modal-title').html('<i class="fa fa-eye"></i> Detail Data {{ $page->title }}');
    $('.submit-data').hide();
</script>
