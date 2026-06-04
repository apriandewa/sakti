$(document).ready(function () {

    $('#datatable').DataTable({
        searchDelay: 500,
        responsive: true,
        lengthChange: true,
        searching: true,
        processing: true,
        serverSide: true,

        lengthMenu: [[10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500, "All"]],

        ajax: {
            url: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
        },

        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'user',
                name: 'user',
                defaultContent: '-'
            },
            {
                data: 'action',
                name: 'action',
                defaultContent: '-'
            },
            {
                data: 'keterangan',
                name: 'keterangan',
                defaultContent: '-'
            },
            {
                data: 'waktu',
                name: 'waktu',
                defaultContent: '-'
            },
            {
                data: 'action_btn',
                name: 'action_btn',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],

        dom: 'lBfrtip',

        buttons: [
            {
                extend: 'csv',
                className: 'btn btn-success btn-xs',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excel',
                className: 'btn btn-info btn-xs',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdf',
                className: 'btn btn-warning btn-xs',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                className: 'btn btn-danger btn-xs',
                exportOptions: { columns: ':visible' }
            }
        ]
    });

});