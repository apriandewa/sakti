$(document).ready(function () {
    'use strict';

    // Konfigurasi URL & CSRF token disiapkan dari index.blade.php lewat window.PresensiConfig,
    // karena file ini statis (bukan .blade.php) sehingga tidak bisa pakai syntax Blade langsung.
    var cfg = window.PresensiConfig || {};

    $('.select2').select2({ width: '100%' });

    let dtPresensi = null;
    let dtLog = null;

    // ============================================================
    //  SELECT2 - Daftar Kantor/OPD (Autocomplete AJAX)
    // ============================================================
    $('.select2-kantor').select2({
        placeholder: 'Ketik nama kantor / OPD...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 0,
        ajax: {
            url: cfg.urls.kantor,
            dataType: 'json',
            delay: 300,
            data: function (p) { return { q: p.term || '' }; },
            processResults: function (d) { return { results: d.results || [] }; },
            cache: true
        }
    });

    // ============================================================
    //  DATATABLE - Rekapitulasi Presensi
    // ============================================================
    function renderDatatablePresensi(kantorId, bulan, tahun) {
        if (dtPresensi) {
            dtPresensi.destroy();
            $('#datatablePresensi tbody').empty();
        }

        dtPresensi = $('#datatablePresensi').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: cfg.urls.data,
                type: 'GET',
                data: { kantor_id: kantorId, bulan: bulan, tahun: tahun }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama_nip', name: 'nama', orderable: true, searchable: true },

                // Status Kehadiran
                { data: 'hadir', name: 'hadir', className: 'text-center font-weight-bold text-success' },
                { data: 'tk', name: 'tk', className: 'text-center font-weight-bold text-danger' },
                { data: 'cuti', name: 'cuti', className: 'text-center font-weight-bold text-info' },
                { data: 'dl', name: 'dl', className: 'text-center font-weight-bold text-primary' },
                { data: 'izin', name: 'izin', className: 'text-center font-weight-bold text-secondary' },
                { data: 'hari_kerja', name: 'hari_kerja', className: 'text-center font-weight-bold' },

                // Terlambat
                { data: 'tm1', name: 'tm1', className: 'text-center text-warning font-weight-bold' },
                { data: 'tm2', name: 'tm2', className: 'text-center text-warning font-weight-bold' },
                { data: 'tm3', name: 'tm3', className: 'text-center text-warning font-weight-bold' },
                { data: 'tm4', name: 'tm4', className: 'text-center text-warning font-weight-bold' },
                { data: 'tmm', name: 'tmm', className: 'text-center text-warning font-weight-bold' },

                // Pulang Cepat
                { data: 'pc1', name: 'pc1', className: 'text-center text-warning font-weight-bold' },
                { data: 'pc2', name: 'pc2', className: 'text-center text-warning font-weight-bold' },
                { data: 'pc3', name: 'pc3', className: 'text-center text-warning font-weight-bold' },
                { data: 'pc4', name: 'pc4', className: 'text-center text-warning font-weight-bold' },
                { data: 'pcm', name: 'pcm', className: 'text-center text-warning font-weight-bold' },

                // Potongan & Aksi
                { data: 'total_potongan_fmt', name: 'total_potongan', className: 'text-center', orderable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center w-0' }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            dom: 'lBfrtip',
            buttons: [
                { extend: 'excel', text: '<i class="fa fa-file-excel-o"></i> Excel', className: 'btn btn-info btn-xs', exportOptions: { columns: ':visible' } },
                { extend: 'pdf', text: '<i class="fa fa-file-pdf-o"></i> PDF', className: 'btn btn-warning btn-xs', exportOptions: { columns: ':visible' } },
                { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn btn-danger btn-xs me-10', exportOptions: { columns: ':visible' } }
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            responsive: true,
        });
    }

    $('#btnCari').on('click', function () {
        var kantorId  = $('#selectKantor').val();
        var bulan     = $('#selectBulan').val();
        var tahun     = $('#selectTahun').val();
        var kantorTxt = $('#selectKantor').find('option:selected').text() || '—';
        var namaBulan = $('#selectBulan option:selected').text();

        if (! kantorId) {
            swal('Pilih Kantor', 'Silakan pilih kantor/OPD terlebih dahulu.', 'warning');
            return;
        }

        $('#judulRekap').html('Rekapitulasi Presensi &amp; Potongan Pegawai &mdash; ' + kantorTxt + ' &mdash; ' + namaBulan + ' ' + tahun);
        renderDatatablePresensi(kantorId, bulan, tahun);
    });

    // ============================================================
    //  DATATABLE - Log & Monitoring Sinkronisasi BKN
    // ============================================================
    $('#tabLogTrigger').on('shown.bs.tab', function () {
        if (! dtLog) {
            dtLog = $('#datatableLog').DataTable({
                processing: true,
                serverSide: true,
                ajax: cfg.urls.logsData,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'kantor', name: 'kantor' },
                    { data: 'bulan', name: 'bulan', className: 'text-center' },
                    { data: 'tahun', name: 'tahun', className: 'text-center' },
                    { data: 'sync_by', name: 'sync_by', className: 'text-center' },
                    { data: 'waktu_mulai_fmt', name: 'waktu_mulai', className: 'text-center' },
                    { data: 'waktu_selesai_fmt', name: 'waktu_selesai', className: 'text-center' },
                    { data: 'jumlah_data_ditarik', name: 'jumlah_data_ditarik', className: 'text-center' },
                    { data: 'status_badge', name: 'status', className: 'text-center' },
                    { data: 'catatan_pesan', name: 'catatan_pesan' }
                ],
                order: [[5, 'desc']],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
                responsive: true,
            });
        } else {
            dtLog.ajax.reload(null, false);
        }
    });

    // Menyesuaikan lebar kolom DataTable saat berpindah tab Bootstrap
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

    // ============================================================
    //  SYNC MANUAL - Tarik Data BKN (per kantor)
    // ============================================================
    $('#btnSync').on('click', function () {
        var kantorId = $('#selectKantorSync').val();
        var bulan    = $('#selectBulanSync').val();
        var tahun    = $('#selectTahunSync').val();

        if (! kantorId) {
            swal('Pilih Kantor', 'Silakan pilih kantor/OPD terlebih dahulu.', 'warning');
            return;
        }

        swal({
            title: 'Tarik Data BKN?',
            text: 'Proses sinkronisasi mungkin memakan beberapa menit. Lanjutkan?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Ya, Tarik!',
            cancelButtonText: 'Batal',
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function (ok) {
            if (! ok) return;

            $.ajax({
                url: cfg.urls.sync,
                method: 'POST',
                data: { _token: cfg.csrfToken, kantor_id: kantorId, bulan: bulan, tahun: tahun },
                success: function (res) {
                    swal('Selesai!', res.message, 'success');
                    if (dtPresensi) dtPresensi.ajax.reload();
                    if (dtLog) dtLog.ajax.reload(null, false);
                },
                error: function (xhr) {
                    swal('Gagal', xhr.responseJSON?.message || 'Gagal menarik data dari BKN.', 'error');
                }
            });
        });
    });

    // Catatan: tombol "Detail" sekarang pakai pola btn-action generik
    // (data-title/data-url/data-size), sudah ditangani oleh jquery-crud.js
    // secara otomatis - tidak perlu handler custom lagi di sini.

    // ============================================================
    //  SYNC SATU PEGAWAI - Tombol per baris
    // ============================================================
    $(document).on('click', '.btn-sync-pegawai', function () {
        var pegawaiId = $(this).data('id');
        var bulan     = $('#selectBulan').val();
        var tahun     = $('#selectTahun').val();
        var $btn      = $(this);

        if (! pegawaiId) {
            swal('Error', 'ID pegawai tidak ditemukan.', 'error');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: cfg.urls.syncPegawai,
            method: 'POST',
            data: { _token: cfg.csrfToken, pegawai_id: pegawaiId, bulan: bulan, tahun: tahun },
            success: function (res) {
                $btn.prop('disabled', false).html('<i class="fa fa-sync-alt"></i>');
                swal({ title: 'Selesai!', text: res.message, type: 'success', timer: 1500 });
                if (dtPresensi) dtPresensi.ajax.reload();
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-sync-alt"></i>');
                swal('Gagal', xhr.responseJSON?.message || 'Gagal sync pegawai.', 'error');
            }
        });
    });

    // ============================================================
    //  IMPORT CSV PEGAWAI
    // ============================================================
    $('#btnImportCsv').on('click', function () {
        var kantorId = $('#selectKantor').val();
        if (! kantorId) {
            swal('Pilih Kantor', 'Pilih kantor terlebih dahulu sebelum import.', 'warning');
            return false;
        }
        $('#csvKantorId').val(kantorId);
    });

    $('#formImportCsv').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: cfg.urls.importCsv,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#modalImportCsv').modal('hide');
                swal('Selesai!', res.message, 'success');
            },
            error: function (xhr) {
                swal('Gagal', xhr.responseJSON?.message || 'Import gagal.', 'error');
            }
        });
    });

    $.fn.dataTable.render.text = function () {
        return function (data, type) {
            if (type === 'display') return data;
            return $('<div>').html(data).text();
        };
    };
});