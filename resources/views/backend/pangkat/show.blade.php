<div class="panel shadow-sm">
    <div class="panel-body">
        <table class="table table-striped">
            <tr>
                <th width="30%">Nama Pangkat</th>
                <td>{{ $data->nama }}</td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $data->desc ?? '-' }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $data->keterangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($data->status == 'aktif')
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-danger">Tidak Aktif</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
<script>
    $('.modal-title').html('<i class="fa fa-eye"></i> Detail Data {{ $page->title }}');
    $('.submit-data').hide();
</script>
