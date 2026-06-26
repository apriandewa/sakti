$(document).ready(function () {
	$('#datatable').DataTable({
        searchDelay: 2000,
		responsive: true,
		lengthChange: true,
        searching   : true,
		processing: true,
		serverSide: true,
        lengthMenu: [[10, 25, 50, 100 ,200 , 500, -1], [10, 25, 50, 100 ,200 , 500, "All"]],
		ajax: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
		columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { 
                data: 'nama',
                render: function(data, type, row) {
                    let dep = row.gelar_depan ? row.gelar_depan + ' ' : '';
                    let bel = row.gelar_belakang ? ', ' + row.gelar_belakang : '';
                    return dep + data + bel;
                }
            },
			{ 
                data: 'nip',
                render: function(data, type, row) {
                    let nipStr = data ? 'NIP. ' + data : '';
                    let nikStr = row.nik ? 'NIK. ' + row.nik : '';
                    if (nipStr && nikStr) {
                        return nipStr + '<br><small class="text-muted">' + nikStr + '</small>';
                    }
                    return nipStr || nikStr || '-';
                }
            },
			{ data: 'pangkat.nama', 'defaultContent': '-' },
			{ data: 'jabatan_nama.nama', 'defaultContent': '-' },
			{ data: 'bidang.nama', 'defaultContent': '-' },
			{ data: 'status_pegawai.nama', 'defaultContent': '-' },
			{ data: 'status', 'defaultContent': '', className: 'text-center' },
			{ data: 'action', orderable: false, searchable: false, className: 'text-center' }
		],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'csv',
                text: 'CSV',
                className: 'btn btn-success btn-xs ms-10',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                text: 'Excel',
                className: 'btn btn-info btn-xs',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'btn btn-warning btn-xs',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-danger btn-xs me-10',
                exportOptions: { columns: ':visible' }
            }
        ]
	});
});
