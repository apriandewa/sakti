{!! html()->modelForm($data,'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
            {!! html()->text('nama',$data->nama)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
        </div>
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('slug')->text('Slug') !!}
            {!! html()->text('slug',$data->slug)->placeholder('Type Slug here')->class('form-control')->id('slug')->attribute('readonly', true) !!}
        </div>
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('desc')->text('Deskripsi') !!}
            {!! html()->textarea('desc',$data->desc)->class('form-control')->id('desc') !!}
        </div>
        <div class="form-group">
            {!! html()->label('Kategori')->class('control-label')->for('kategori') !!}
            {!! html()->select('kategori', $kategoris ?? [])->value($data->kategori ?? '')->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
        </div>

        {{-- ========== UPLOAD GAMBAR SAMPUL (SINGLE) ========== --}}
        <div class='form-group'>
            {!! html()->label()->class('control-label')->for('gambar')->text('Upload Gambar Sampul Baru') !!}
            <span class="text-muted">(Kosongkan jika tidak ingin mengganti)</span>
            <small class="text-muted d-block mb-1">Format: jpg, jpeg, png. Maks 2MB.</small>
            <div class="logo-upload-area" id="logoUploadArea">
                <label for="gambar" class="logo-drop-label" id="logoDropLabel">
                    <i class="fa fa-cloud-upload fa-2x text-muted"></i>
                    <span class="d-block mt-1 text-muted">Klik untuk pilih gambar sampul baru</span>
                </label>
                <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg" class="d-none">
            </div>
            <div id="logoPreviewWrap" class="mt-2 d-none">
                <div class="logo-thumb-card">
                    <img id="logoPreviewImg" src="#" alt="Preview Gambar" class="logo-thumb-img">
                    <div class="logo-thumb-actions">
                        <button type="button" class="btn btn-xs btn-info" id="btnZoomLogo" title="Perbesar"><i class="fa fa-search-plus"></i></button>
                        <button type="button" class="btn btn-xs btn-danger" id="btnRemoveLogo" title="Hapus"><i class="fa fa-times"></i></button>
                    </div>
                    <p class="logo-thumb-name" id="logoPreviewName"></p>
                </div>
            </div>
        </div>

        {{-- Gambar saat ini --}}
        @php $gambarFile = $data->files->where('alias', 'gambar_unduhan')->first(); @endphp
        @if($gambarFile)
            <div class="form-group mb-4">
                <label class="control-label">Gambar Sampul Saat Ini :</label>
                <div class="logo-thumb-card" style="display:block;">
                    <img src="{{ url($gambarFile->link_stream ?? '#') }}" alt="gambar" class="logo-thumb-img">
                    <div class="logo-thumb-actions">
                        <a href="{{ url($gambarFile->link_stream ?? '#') }}" target="_blank" class="btn btn-xs btn-info" title="Lihat"><i class="fa fa-search-plus"></i></a>
                    </div>
                    <p class="logo-thumb-name">{{ $gambarFile->data['name'] ?? 'gambar' }}</p>
                </div>
            </div>
        @endif

        {{-- ========== UPLOAD BERKAS BARU (MULTIPLE — PDF / WORD) ========== --}}
        <div class='form-group'>
            {!! html()->label('Tambah Berkas Unduhan Baru','berkas[]')->class('control-label') !!}
            <span class="text-muted">(Kosongkan jika tidak ingin menambah berkas)</span>
            <small class="text-muted d-block mb-1">Format: PDF, DOC, DOCX. Maks 20MB per file.</small>
            <div class="berkas-drop-zone" id="berkasDropZone">
                <i class="fa fa-file-pdf-o fa-2x text-muted"></i>
                <p class="mb-1 mt-2 text-muted">Drag &amp; Drop berkas ke sini, atau</p>
                <label for="berkasTrigger" class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
                    <i class="fa fa-folder-open"></i> Pilih File
                </label>
                <input type="file" id="berkasTrigger" accept=".pdf,.doc,.docx" multiple class="d-none">
                <input type="file" name="berkas[]" id="berkasInput" accept=".pdf,.doc,.docx" multiple class="d-none">
            </div>
            <div id="berkasFileList" class="berkas-file-list"></div>
        </div>

        {{-- Berkas unduhan saat ini --}}
        @php $berkasFiles = $data->files->where('alias', 'berkas_unduhan'); @endphp
        @if($berkasFiles->count())
            <div class="form-group">
                <label class="control-label">Berkas Unduhan Saat Ini :</label>
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
    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    initLogoUpload('gambar', 'logoPreviewWrap', 'logoPreviewImg', 'logoPreviewName', 'btnRemoveLogo', 'btnZoomLogo', 'modalZoomPreview');
    initBerkasUpload('berkasTrigger', 'berkasInput', 'berkasDropZone', 'berkasFileList', true);
</script>

<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>
