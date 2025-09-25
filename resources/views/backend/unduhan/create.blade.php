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
			{!! html()->label()->class('control-label')->for('desc')->text('Desc') !!}
			{!! html()->textarea('desc',NULL)->class('form-control')->id('desc') !!}
		</div>
		<div class="form-group">
            {!! html()->label('Kategori')->class('control-label')->for('kategori') !!}
            
            {!! html()->select('kategori', [
                'Dasar Hukum' => 'Dasar Hukum',
                'Pengumuman' => 'Pengumuman',
                'Unduhan' => 'Unduhan',
                'Laporan Tahunan' => 'Laporan Tahunan'
            ])->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
        </div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('gambar')->text('Upload Gambar') !!}
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/jpeg,image/png') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('file')->text('Upload Berkas Unduhan') !!}
            {!! html()->file('file')->class('form-control')->id('file')->accept('application/pdf') !!}
		</div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{{--{!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!}--}}
{{--{!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!}--}}
{!! html()->form()->close() !!}
<style>
    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.select2').select2();
    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');
</script>

<script>
    function slugify(text) {
        return text
            .toString()                 // pastikan string
            .normalize('NFD')           // handle huruf dengan aksen
            .replace(/[\u0300-\u036f]/g, '') 
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9]+/g, '-') // ganti non-alfanumerik jadi -
            .replace(/^-+|-+$/g, '');    // hapus - di awal/akhir
    }

    document.getElementById('nama').addEventListener('keyup', function() {
        let nama = this.value;
        document.getElementById('slug').value = slugify(nama);
    });
</script>

