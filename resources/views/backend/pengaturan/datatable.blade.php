$(document).ready(function () {
	$('#datatable').DataTable({
        
		responsive: true,
		ajax: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
		language: {
            {{-- Uncomment this line to use Indonesian language --}}
            url: "{{ asset(config('master.app.web.assets').'/assets/vendor_components/datatable/indonesian.json') }}"
        },
		columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, orderable: false, className: 'text-center' },
            { data: 'judul' , 'defaultContent':''},
			{ data: 'action', orderable: false, searchable: false , className: 'text-center'}
		],
        
	});
})
