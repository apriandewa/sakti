{{ html()->modelForm($data, 'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="form-group">
            {!! html()->label('Nama Informasi', 'nama')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->text('nama')->class('form-control')->id('nama')->placeholder('Nama Informasi')->required() !!}
        </div>
        
        <div class="form-group">
            {!! html()->label('Tipe', 'tipe')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->select('tipe', [
                'BERKALA' => 'BERKALA',
                'TERSEDIA' => 'TERSEDIA SETIAP SAAT',
                'SERTA MERTA' => 'SERTA MERTA',
                'DIKECUALIKAN' => 'DIKECUALIKAN'
            ])->class('form-control select2')->required() !!}
        </div>

        <div class="form-group">
            {!! html()->label('Tahun', 'tahun')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->number('tahun')->class('form-control')->required() !!}
        </div>

        <div class="form-group">
            {!! html()->label('Deskripsi', 'desc')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->textarea('desc')->class('form-control')->id('desc')->placeholder('Ketik Disini')->required() !!}
        </div>

        <div class="form-group">
            {!! html()->label('Tambah Berkas Informasi Baru (PDF, Max 5MB)','berkas_informasi[]')->class('control-label') !!}
            <span class="text-muted">(Kosongkan jika tidak ingin menambah berkas)</span>
            <small class="text-muted d-block mb-1">Format: PDF. Maks 5MB per file.</small>
            <div class="berkas-drop-zone" id="berkasDropZone">
                <i class="fa fa-file-pdf-o fa-2x text-muted"></i>
                <p class="mb-1 mt-2 text-muted">Drag &amp; Drop berkas ke sini, atau</p>
                <label for="berkasTrigger" class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
                    <i class="fa fa-folder-open"></i> Pilih File
                </label>
                <input type="file" id="berkasTrigger" accept=".pdf,application/pdf" multiple class="d-none">
                <input type="file" name="berkas_informasi[]" id="berkasInput" accept=".pdf,application/pdf" multiple class="d-none">
            </div>
            <div id="berkasFileList" class="berkas-file-list"></div>
        </div>

        @php $berkasFiles = $data->files->where('alias', 'berkas_informasi'); @endphp
        @if($berkasFiles->count())
            <div class="form-group">
                <label class="control-label">Berkas Informasi Saat Ini :</label>
                <table class="table table-sm table-bordered">
                    <thead><tr><th>Nama File</th><th class="text-center w-0">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($berkasFiles as $berkas)
                            <tr id="file-row-{{ $berkas->id }}">
                                <td><i class="fa fa-file-pdf-o text-danger"></i> {{ $berkas->data['name'] ?? $berkas->id }}</td>
                                <td class="text-center">
                                    <a href="{{ url($berkas->link_stream) }}" target="_blank" class="btn btn-xs btn-info" title="Lihat"><i class="fa fa-eye"></i></a>
                                    <a href="#delete" class="delete-file btn btn-xs btn-danger" data-id="file-row-{{ $berkas->id }}" data-url="{{ url($berkas->link_delete) }}" data-title="Hapus Berkas" data-message="Apakah Anda yakin ingin menghapus berkas ini?" title="Hapus"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->closeModelForm() !!}

<style>
    .select2-container {
        z-index: 999999 !important;
        width: 100% !important;
    }
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({ placeholder: "Silahkan Pilih", dropdownParent: dropdownParent });
    });
    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    initBerkasUpload('berkasTrigger', 'berkasInput', 'berkasDropZone', 'berkasFileList', true);
</script>

<script src="{{ url($template.'/js/forminput.js') }}"></script>
