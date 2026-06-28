$(document).ready(function () {
    $('.select2').select2({
        width: '100%'
    });

    // 1. Inisialisasi DataTable Rekap Kehadiran
    const table = $('#datatable').DataTable({
        searchDelay: 1000,
        responsive: true,
        lengthChange: true,
        searching: true,
        processing: true,
        serverSide: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        ajax: {
            url: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
            data: function (d) {
                d.month = $('#month-filter').val();
                d.year = $('#year-filter').val();
                d.source = $('#source-filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama_nip', name: 'nama', orderable: true, searchable: true },
            
            // Kehadiran (HN, TK, CT, DL, Izin, Hari Kerja)
            { data: 'count_hn', name: 'count_hn', className: 'text-center font-weight-bold text-success' },
            { data: 'count_tk', name: 'count_tk', className: 'text-center font-weight-bold text-danger' },
            { data: 'count_ct', name: 'count_ct', className: 'text-center font-weight-bold text-info' },
            { data: 'count_dl', name: 'count_dl', className: 'text-center font-weight-bold text-primary' },
            { data: 'count_izin', name: 'count_izin', className: 'text-center font-weight-bold text-secondary' },
            { data: 'total_hari_kerja', name: 'total_hari_kerja', className: 'text-center font-weight-bold' },
            
            // Terlambat
            { data: 'count_tm1', name: 'count_tm1', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_tm2', name: 'count_tm2', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_tm3', name: 'count_tm3', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_tm4', name: 'count_tm4', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_tmm', name: 'count_tmm', className: 'text-center text-warning font-weight-bold' },
            
            // Pulang Cepat
            { data: 'count_pc1', name: 'count_pc1', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_pc2', name: 'count_pc2', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_pc3', name: 'count_pc3', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_pc4', name: 'count_pc4', className: 'text-center text-warning font-weight-bold' },
            { data: 'count_pc5', name: 'count_pc5', className: 'text-center text-warning font-weight-bold' },
            
            // Potongan & Aksi
            { data: 'total_potongan', name: 'total_potongan', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center w-0' }
        ],
        dom: 'lBfrtip',
        buttons: [
            { 
                extend: 'excel', 
                text: '<i class="fa fa-file-excel-o"></i> Excel', 
                className: 'btn btn-info btn-xs', 
                exportOptions: { columns: ':visible' } 
            },
            { 
                extend: 'pdf', 
                text: '<i class="fa fa-file-pdf-o"></i> PDF', 
                className: 'btn btn-warning btn-xs', 
                exportOptions: { columns: ':visible' } 
            },
            { 
                extend: 'print', 
                text: '<i class="fa fa-print"></i> Print', 
                className: 'btn btn-danger btn-xs me-10', 
                exportOptions: { columns: ':visible' } 
            }
        ]
    });

    // 2. Inisialisasi DataTable Log Sinkronisasi
    const syncLogsTable = $('#sync-logs-table').DataTable({
        searchDelay: 1000,
        responsive: true,
        processing: true,
        serverSide: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        ajax: {
            url: "{{ url(config('master.app.url.backend').'/presensi/sync-logs') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'created_at', name: 'created_at', className: 'text-center' },
            { data: 'periode', name: 'periode', className: 'text-center font-weight-bold' },
            { data: 'triggered_by', name: 'triggered_by' },
            { data: 'total_pegawai_synced', name: 'total_pegawai_synced', className: 'text-center text-success font-weight-bold' },
            { data: 'total_pegawai_skipped', name: 'total_pegawai_skipped', className: 'text-center text-danger font-weight-bold' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'message', name: 'message' }
        ]
    });

    // Menangani penyesuaian kolom DataTable saat berpindah Tab Bootstrap
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

    // Memicu pencarian rekapitulasi data
    $('#form-filter').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Event handler tombol sinkronisasi cepat (Sync BKN) di filter utama
    $('#btn-sync-bkn').on('click', function () {
        const month = $('#month-filter').val();
        const year = $('#year-filter').val();

        swal({
            title: "Tarik Data Simpegnas?",
            text: "Sistem akan menarik data kehadiran pegawai dari API BKN untuk bulan " + $('#month-filter option:selected').text() + " / " + year + " ke Database lokal.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#059669",
            confirmButtonText: "Ya, Tarik!",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if (!isConfirm) return;

            $.ajax({
                url: "{{ url(config('master.app.url.backend').'/presensi/sync') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    month: month,
                    year: year
                },
                success: function (res) {
                    if (res.status === true) {
                        swal("Berhasil!", res.message, "success");
                        table.ajax.reload();
                        syncLogsTable.ajax.reload();
                    } else {
                        swal("Gagal!", res.message, "error");
                    }
                },
                error: function (xhr) {
                    swal("Error!", "Gagal menghubungi server API. Kode: " + xhr.status, "error");
                }
            });
        });
    });

    // Event handler form Sinkronisasi Manual Terjadwal di Tab Monitoring Log
    $('#form-manual-sync').on('submit', function (e) {
        e.preventDefault();
        const month = $('#sync-month').val();
        const year = $('#sync-year').val();
        const btnSubmit = $('#btn-start-sync');

        btnSubmit.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-5"></i> Sinkronisasi Berjalan...');

        swal({
            title: "Konfirmasi Sinkronisasi?",
            text: "Tarik data rekap seluruh pegawai untuk periode " + $('#sync-month option:selected').text() + " " + year + "? Proses ini mungkin memakan waktu beberapa detik.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#059669",
            confirmButtonText: "Mulai Sync",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if (!isConfirm) {
                btnSubmit.prop('disabled', false).html('<i class="fa fa-cloud-download me-5"></i> Mulai Sinkronisasi Manual');
                return;
            }

            $.ajax({
                url: "{{ url(config('master.app.url.backend').'/presensi/sync') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    month: month,
                    year: year
                },
                success: function (res) {
                    btnSubmit.prop('disabled', false).html('<i class="fa fa-cloud-download me-5"></i> Mulai Sinkronisasi Manual');
                    if (res.status === true) {
                        swal("Sinkronisasi Sukses!", res.message, "success");
                        table.ajax.reload();
                        syncLogsTable.ajax.reload();
                    } else {
                        swal("Gagal Sinkronisasi!", res.message, "error");
                    }
                },
                error: function (xhr) {
                    btnSubmit.prop('disabled', false).html('<i class="fa fa-cloud-download me-5"></i> Mulai Sinkronisasi Manual');
                    swal("Error!", "Terjadi kesalahan internal saat menarik data dari BKN.", "error");
                }
            });
        });
    });

    // Event handler tombol sinkronisasi per pegawai (Sync Single)
    $(document).on('click', '.btn-sync-single', function () {
        const id = $(this).data('id');
        const month = $('#month-filter').val();
        const year = $('#year-filter').val();
        const btn = $(this);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ url(config('master.app.url.backend').'/presensi/sync') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                month: month,
                year: year,
                pegawai_id: id
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i>');
                if (res.status === true) {
                    swal({
                        title: "Sukses!",
                        text: "Data pegawai berhasil diperbarui.",
                        type: "success",
                        timer: 1500
                    });
                    table.ajax.reload();
                } else {
                    swal("Gagal!", res.message, "error");
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i>');
                swal("Error!", "Gagal menghubungi server API.", "error");
            }
        });
    });
});
