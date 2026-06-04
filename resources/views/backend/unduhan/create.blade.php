{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
            {!! html()->text('nama',NULL)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
        </div>
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('slug')->text('Slug') !!}
            {!! html()->text('slug',NULL)->placeholder('Auto generated')->class('form-control')->id('slug')->attribute('readonly', true) !!}
        </div>
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('desc')->text('Deskripsi') !!}
            {!! html()->textarea('desc',NULL)->class('form-control')->id('desc') !!}
        </div>
        <div class="form-group">
            {!! html()->label('Kategori')->class('control-label')->for('kategori') !!}
            {!! html()->select('kategori', $kategoris ?? [])->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
        </div>

        {{-- ========== UPLOAD GAMBAR SAMPUL (SINGLE) ========== --}}
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('gambar')->text('Upload Gambar Sampul') !!}
            <small class="text-muted d-block mb-1">Format: jpg, jpeg, png. Maks 2MB.</small>
            <div class="logo-upload-area" id="logoUploadArea">
                <label for="gambar" class="logo-drop-label" id="logoDropLabel">
                    <i class="fa fa-cloud-upload fa-2x text-muted"></i>
                    <span class="d-block mt-1 text-muted">Klik untuk pilih gambar sampul</span>
                </label>
                <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg" class="d-none">
            </div>
            {{-- Preview Gambar --}}
            <div id="logoPreviewWrap" class="mt-2 d-none">
                <div class="logo-thumb-card">
                    <img id="logoPreviewImg" src="#" alt="Preview Gambar" class="logo-thumb-img">
                    <div class="logo-thumb-actions">
                        <button type="button" class="btn btn-xs btn-info" id="btnZoomLogo" title="Perbesar">
                            <i class="fa fa-search-plus"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger" id="btnRemoveLogo" title="Hapus">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <p class="logo-thumb-name" id="logoPreviewName"></p>
                </div>
            </div>
        </div>

        {{-- ========== UPLOAD BERKAS UNDUHAN (MULTIPLE — PDF / WORD) ========== --}}
        <div class='form-group'>
            {!! html()->label('Upload Berkas Unduhan','berkas[]')->class('control-label') !!}
            <small class="text-muted d-block mb-1">
                Format: PDF, DOC, DOCX. Maks 20MB per file. Bisa pilih banyak sekaligus.
            </small>
            <div class="berkas-drop-zone" id="berkasDropZone">
                <i class="fa fa-file-pdf-o fa-2x text-muted"></i>
                <p class="mb-1 mt-2 text-muted">Drag &amp; Drop berkas ke sini, atau</p>
                <label for="berkasTrigger" class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
                    <i class="fa fa-folder-open"></i> Pilih File
                </label>
                <input type="file" id="berkasTrigger" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple class="d-none">
                <input type="file" name="berkas[]" id="berkasInput" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" multiple class="d-none">
            </div>
            {{-- Daftar file berkas yang dipilih --}}
            <div id="berkasFileList" class="berkas-file-list"></div>
        </div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->form()->close() !!}

{{-- MODAL ZOOM PREVIEW (Gambar Sampul) --}}
<div class="modal fade" id="modalZoomPreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center pt-0">
                <img id="zoomPreviewImg" src="#" alt="Preview" class="img-fluid rounded" style="max-height:70vh;">
                <p id="zoomPreviewName" class="text-white mt-2 mb-0 small"></p>
            </div>
        </div>
    </div>
</div>

<style>
    .select2-container { z-index: 999999 !important; width: 100% !important; }
    .modal-lg { max-width: 1000px !important; }
</style>

<script>
    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({ placeholder: "Silahkan Pilih", dropdownParent: dropdownParent });
    });
    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    initLogoUpload('gambar', 'logoPreviewWrap', 'logoPreviewImg', 'logoPreviewName', 'btnRemoveLogo', 'btnZoomLogo', 'modalZoomPreview');
    initBerkasUpload('berkasTrigger', 'berkasInput', 'berkasDropZone', 'berkasFileList', true);
</script>

<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>
