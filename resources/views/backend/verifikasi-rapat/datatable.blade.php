$(document).ready(function () {
	$('#datatable').DataTable({
        searchDelay: 2000, responsive: true, lengthChange: true, searching: true, processing: true, serverSide: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
		ajax: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
		columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama', defaultContent: '' },
            { data: 'tanggal', defaultContent: '' },
            { data: 'waktu', defaultContent: '', orderable: false },
            { data: 'tempat', defaultContent: '' },
            { data: 'pembuat', defaultContent: '' },
			{ data: 'status', className: 'text-center' },
			{ data: 'action', orderable: false, searchable: false, className: 'text-center' }
		]
	});
})
