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
			{!! html()->label()->class('control-label')->for('keterangan')->text('Keterangan') !!}
			{!! html()->text('keterangan',NULL)->placeholder('Type Keterangan here')->class('form-control')->id('keterangan') !!}
		</div>
        <div class="row">
            <div class="col-md-6">
		        <div class="form-group">
                    {!! html()->label('Kategori')->class('control-label')->for('kategori') !!}
                    
                    {!! html()->select('kategori', [
                        'welcome' => 'Welcome',
                        'profil' => 'Profil',
                        'program' => 'Program Kegiatan',
                        'bidang' => 'Bidang',
                        'single_page' => 'Single Page',
                        'lainnya' => 'Lainnya'
                    ])->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Icon','icon')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    <div class="input-group mb-3">
                        <span class="input-group-prepend">
                            <i class="input-group-text selected-icon"></i>
                        </span>
                        {!! html()->text('icon')->placeholder('Icon')->class('form-control iconpicker')->id('icon')->attributes(['autocomplete' => 'off']) !!}
                    </div>
                </div>
            </div>
        </div>
		<div class='form-group'>
            {!! html()->label('Isi Halaman Page','desc')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->textarea('desc')->class('form-control')->id('desc')->placeholder('Ketik Disini')->required() !!}
        </div>
		<div class='form-group'>
            {!! html()->checkbox('menu',false,1)->id('mh_checkbox')->class('filled-in chk-col-primary') !!}
            {!! html()->label('Tampilkan di Menu Header','mh_checkbox')->class('control-label') !!}
            <span class="text-danger">*</span>
        </div>
		<div class='form-group'>
            {!! html()->checkbox('beranda',false,1)->id('md_checkbox')->class('filled-in chk-col-primary') !!}
            {!! html()->label('Tampilkan di Beranda','md_checkbox')->class('control-label') !!}
            <span class="text-danger">*</span>
        </div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('gambar')->text('Upload Gambar') !!}
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/jpeg,image/png') !!}
		<span class="text-danger">Jenis File : jpg, jpeg, png (Maksimal 2MB)</span>
		</div>
		<div class="form-group">
            {!! html()->label('Status')->class('control-label')->for('status') !!}
            
            {!! html()->select('status', [
                'aktif' => 'Aktif',
                'nonaktif' => 'Tidak Aktif'
            ])->placeholder('Pilih status di sini')->class('form-control select2')->id('status') !!}
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

<script src="{{ url($template.'/fileupload/js/fileinput.js') }}"></script>
<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>
<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/assets/vendor_components/bootstrap-iconpicker/dist/iconpicker.js') }}"></script>
<script>
	$('.select2').select2();

    (async () => {
        const response = await fetch("{{ url($template.'/assets/vendor_components/bootstrap-iconpicker/dist/iconsets/fontawesome4.json') }}")
        const result = await response.json()
        const iconpicker = new Iconpicker(document.querySelector(".iconpicker"), {
            icons: result,
            showSelectedIn: document.querySelector(".selected-icon"),
            defaultValue: 'fa-arrow-right',
            valueFormat: val => `fa ${val.replace('fas-', 'fa-')}`,
        });
        iconpicker.set()
        iconpicker.set('fa-arrow-right')
    })()
</script>
