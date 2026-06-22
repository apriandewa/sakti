$(document).ready(function () {
	$('#datatable').DataTable({
        searchDelay: 2000,
		responsive: true,
		lengthChange: true,
        searching: true,
		processing: true,
		serverSide: true,
        lengthMenu: [[10, 25, 50, 100 ,200 , 500, -1], [10, 25, 50, 100 ,200 , 500, "All"]],
		ajax: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
		language: {
        },
		columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama' , 'defaultContent':''},
			{ data: 'tipe' , 'defaultContent':''},
			{ data: 'tahun' , 'defaultContent':''},
			{ data: 'status_badge', name: 'status_badge' },
			{ data: 'action', orderable: false, searchable: false , className: 'text-center'}
		],
        dom: 'lBfrtip',
        buttons: [
            { extend: 'csv', text: 'CSV', className: 'btn btn-success btn-xs ms-10', exportOptions: { columns: ':visible' } },
            { extend: 'excel', text: 'Excel', className: 'btn btn-info btn-xs', exportOptions: { columns: ':visible' } },
            { extend: 'pdf', text: 'PDF', className: 'btn btn-warning btn-xs', exportOptions: { columns: ':visible' } },
            { extend: 'print', text: 'Print', className: 'btn btn-danger btn-xs me-10', exportOptions: { columns: ':visible' } }
        ]
	});

    $(document).on('click', '.btn-kirim', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        swal({
            title: "Apakah anda yakin?",
            text: "Data akan dikirim untuk verifikasi",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Kirim!",
            cancelButtonText: "Batal",
            closeOnConfirm: false
        }, function(){
            $.post(url, {_token: "{{ csrf_token() }}"}, function(res) {
                if (res.status) {
                    swal("Terkirim!", res.message, "success");
                    $('#datatable').DataTable().ajax.reload();
                } else {
                    swal("Gagal!", res.message, "error");
                }
            });
        });
    });
});
