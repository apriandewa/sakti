/**
 * Rekap e-Kinerja — Backend JS
 * 2-Tab: (1) Data Kinerja + (2) Log & Monitoring Sinkronisasi BKN
 *
 * Bergantung pada window.EkinerjaRekapConfig yang di-inject dari Blade
 * (resources/views/backend/kinerja/index.blade.php).
 *
 * Pola identik dengan PresensiController JS (modul Presensi).
 */
$(document).ready(function () {
    var cfg = window.EkinerjaRekapConfig || {};

    /* =================================================================
     * HELPER — Inisialisasi Select2 (Kantor/Unor & Periode)
     * ================================================================= */

    function initSelect2Unor($el) {
        $el.select2({
            placeholder: 'Pilih Kantor / OPD (Unor)',
            width: '100%',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: cfg.urlUnorOptions,
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term || '' }; },
                processResults: function (data) { return { results: data.results || [] }; },
                cache: true
            }
        });
    }

    function initSelect2Periode($el) {
        $el.select2({
            placeholder: 'Pilih Periode SKP',
            width: '100%',
            allowClear: true,
            minimumInputLength: 0,
            ajax: {
                url: cfg.urlPeriodeOptions,
                dataType: 'json',
                delay: 300,
                data: function (params) { return { q: params.term || '' }; },
                processResults: function (data) { return { results: data.results || [] }; },
                cache: true
            }
        });
    }

    /* =================================================================
     * TAB 1 — Select2 & DataTable Data Kinerja
     * ================================================================= */

    var $filterUnor   = $('#filterUnor');
    var $filterPeriode = $('#filterPeriode');
    var $rekapHint    = $('#rekapEmptyHint');
    var tableRekap    = null;

    initSelect2Unor($filterUnor);
    initSelect2Periode($filterPeriode);

    function initTableRekap() {
        $rekapHint.hide();

        if (tableRekap) {
            tableRekap.ajax.reload();
            return;
        }

        tableRekap = $('#datatableRekap').DataTable({
            searchDelay: 600,
            responsive: true,
            lengthChange: true,
            searching: true,
            processing: true,
            serverSide: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-fw"></i> Memuat data...',
                emptyTable: '<i class="fa fa-info-circle"></i> Tidak ada data untuk filter yang dipilih.',
                zeroRecords: '<i class="fa fa-search"></i> Data tidak ditemukan.',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                search: 'Cari:'
            },
            ajax: {
                url: cfg.urlDatatable,
                data: function (d) {
                    d.unor_id   = $filterUnor.val();
                    d.periode_id = $filterPeriode.val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama_nip', name: 'nama', defaultContent: '-' },
                { data: 'unor_nama', name: 'skp_unor', defaultContent: '-' },
                { data: 'periode_skp', name: 'periode_skp', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'hasil_kerja_badge', name: 'hasil_kerja', orderable: false, searchable: false, className: 'text-center' },
                { data: 'perilaku_kerja_badge', name: 'perilaku_kerja', orderable: false, searchable: false, className: 'text-center' },
                { data: 'hasil_akhir_badge', name: 'hasil_akhir', orderable: false, searchable: false, className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ]
        });
    }

    /* Submit filter Tab 1 */
    $('#formFilterRekap').on('submit', function (e) {
        e.preventDefault();
        if (!$filterUnor.val() || !$filterPeriode.val()) {
            swal('Filter belum lengkap', 'Silakan pilih Kantor/Unor dan Periode terlebih dahulu.', 'warning');
            return;
        }
        initTableRekap();
    });

    /* =================================================================
     * TAB 2 — Select2 & DataTable Log Sinkronisasi
     * ================================================================= */

    var $syncUnor   = $('#syncUnor');
    var $syncPeriode = $('#syncPeriode');
    var $btnSync    = $('#btnSyncRekap');
    var tableLogs   = null;

    initSelect2Unor($syncUnor);
    initSelect2Periode($syncPeriode);

    // Nonaktifkan tombol sync saat halaman pertama dibuka
    $btnSync.prop('disabled', true);

    function toggleSyncButton() {
        $btnSync.prop('disabled', !($syncUnor.val() && $syncPeriode.val()));
    }
    $syncUnor.on('change', toggleSyncButton);
    $syncPeriode.on('change', toggleSyncButton);

    function initTableLogs() {
        if (tableLogs) {
            tableLogs.ajax.reload(null, false);
            return;
        }

        tableLogs = $('#datatableLogs').DataTable({
            searchDelay: 600,
            responsive: true,
            lengthChange: true,
            processing: true,
            serverSide: true,
            order: [[5, 'desc']], // urutkan berdasarkan waktu_mulai DESC
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-fw"></i> Memuat data...',
                emptyTable: 'Belum ada histori sinkronisasi.',
                zeroRecords: 'Data tidak ditemukan.',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_-_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                search: 'Cari:'
            },
            ajax: {
                url: cfg.urlLogsDatatable,
                data: function (d) {
                    d.unor_id    = $syncUnor.val() || '';
                    d.periode_id = $syncPeriode.val() || '';
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama_unor', name: 'nama_unor', defaultContent: '-' },
                { data: 'periode_id', name: 'periode_id', defaultContent: '-' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                { data: 'sync_by', name: 'sync_by', defaultContent: '-' },
                { data: 'waktu_mulai_fmt', name: 'waktu_mulai', defaultContent: '-' },
                { data: 'waktu_selesai_fmt', name: 'waktu_selesai', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'durasi_fmt', name: 'durasi', orderable: false, searchable: false, className: 'text-center', defaultContent: '-' },
                { data: 'jumlah_fmt', name: 'jumlah', orderable: false, searchable: false, className: 'text-center' },
                { data: 'catatan_pesan', name: 'catatan_pesan', orderable: false, searchable: false, defaultContent: '-' }
            ]
        });
    }

    /* Muat log saat Tab 2 pertama kali dibuka */
    $('#tabLogLink').on('shown.bs.tab', function () {
        initTableLogs();
    });

    /* Filter log berubah → inisialisasi/reload tabel */
    $syncUnor.on('change', function () {
        if (tableLogs) {
            tableLogs.ajax.reload(null, false);
        }
        // Jika tabel belum diinisialisasi tapi tab sudah aktif, init sekarang
        else if ($('#tabLogSinkronisasi').hasClass('active')) {
            initTableLogs();
        }
    });
    $syncPeriode.on('change', function () {
        if (tableLogs) {
            tableLogs.ajax.reload(null, false);
        } else if ($('#tabLogSinkronisasi').hasClass('active')) {
            initTableLogs();
        }
    });

    /* =================================================================
     * SINKRONISASI MANUAL (PRD Bab 7.2 — Tombol "Tarik Data BKN")
     * ================================================================= */

    $('#formSinkronisasi').on('submit', function (e) {
        e.preventDefault();

        if (!$syncUnor.val() || !$syncPeriode.val()) {
            swal('Filter belum lengkap', 'Silakan pilih Kantor/Unor dan Periode terlebih dahulu.', 'warning');
            return;
        }

        swal({
            title: 'Tarik Data BKN?',
            text: 'Sistem akan menarik ulang data penilaian dari API BKN untuk Kantor/OPD & Periode terpilih. Proses ini mungkin memerlukan waktu beberapa menit.',
            icon: 'warning',
            buttons: ['Batal', 'Ya, Tarik Data']
        }).then(function (confirmed) {
            if (!confirmed) return;

            var originalHtml = $btnSync.html();
            $btnSync.prop('disabled', true).html('<span class="fa fa-spinner fa-spin"></span> Menyinkronkan...');

            $.post(cfg.urlSync, {
                _token:     cfg.csrfToken,
                unor_id:    $syncUnor.val(),
                periode_id: $syncPeriode.val()
            }).done(function (res) {
                var icon = res.status ? 'success' : 'error';
                swal('Selesai', res.message || 'Sinkronisasi selesai.', icon);
                // Reload tabel log — inisialisasi jika belum ada
                if (tableLogs) {
                    tableLogs.ajax.reload(null, false);
                } else {
                    initTableLogs();
                }
                // Reload tabel rekap jika sudah diinisialisasi
                if (tableRekap) tableRekap.ajax.reload(null, false);
            }).fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Sinkronisasi gagal, silakan coba lagi.';
                swal('Gagal', msg, 'error');
            }).always(function () {
                $btnSync.prop('disabled', false).html(originalHtml);
                toggleSyncButton();
            });
        });
    });

    /* =================================================================
     * MODAL DETAIL (shared btn-action handler — mengikuti pola Presensi)
     * ================================================================= */

    $(document).on('click', '.btn-action[data-action="show"], .btn-action[data-url]', function () {
        var url   = $(this).data('url');
        var title = $(this).data('title') || 'Detail';
        var size  = $(this).data('size') || 'modal-lg';

        if (!url) return;

        var $modal = $('#modal-default, .modal-crud').first();
        if (!$modal.length) {
            // Buat modal on-the-fly jika tidak ada
            $modal = $(
                '<div class="modal fade" id="modalEkinerjaDetail" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog ' + size + '" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-header">' +
                '<h5 class="modal-title">' + title + '</h5>' +
                '<button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>' +
                '</div>' +
                '<div class="modal-body" id="modalEkinerjaBody"><div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div></div>' +
                '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>' +
                '</div></div></div>'
            );
            $('body').append($modal);
        }

        $modal.find('.modal-title').html(title);
        $modal.find('.modal-body, #modalEkinerjaBody').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        $modal.modal('show');

        $.get(url).done(function (html) {
            $modal.find('.modal-body, #modalEkinerjaBody').html(html);
        }).fail(function () {
            $modal.find('.modal-body, #modalEkinerjaBody').html('<div class="alert alert-danger">Gagal memuat data detail.</div>');
        });
    });
});
