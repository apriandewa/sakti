{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="form-group">
            {!! html()->label('Nama Informasi', 'nama')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->text('nama', null)->class('form-control')->id('nama')->placeholder('Nama Informasi')->required() !!}
        </div>
        
        <div class="form-group">
            {!! html()->label('Tipe', 'tipe')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->select('tipe', [
                'BERKALA' => 'BERKALA',
                'TERSEDIA' => 'TERSEDIA',
                'SERTA MERTA' => 'SERTA MERTA',
                'DIKECUALIKAN' => 'DIKECUALIKAN'
            ], null)->class('form-control select2')->required() !!}
        </div>

        <div class="form-group">
            {!! html()->label('Tahun', 'tahun')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->number('tahun', date('Y'))->class('form-control')->required() !!}
        </div>

        <div class="form-group">
            {!! html()->label('Deskripsi', 'desc')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->textarea('desc', null)->class('form-control')->id('desc')->placeholder('Ketik Disini')->required() !!}
        </div>

        <div class="form-group">
            {!! html()->label('Berkas Informasi (PDF, Max 5MB)','berkas_informasi[]')->class('control-label') !!}
            <small class="text-muted d-block mb-1">
                Format: PDF. Maks 5MB per file. Bisa pilih banyak sekaligus.
            </small>
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
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

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
    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    initBerkasUpload('berkasTrigger', 'berkasInput', 'berkasDropZone', 'berkasFileList', true);
</script>

<script src="{{ url($template.'/js/forminput.js') }}"></script>
