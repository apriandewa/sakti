/**
 * Rekap e-Kinerja — Backend
 * Bergantung pada window.EkinerjaRekapConfig yang di-inject dari Blade
 * (lihat resources/views/backend/ekinerja/index.blade.php).
 */
$(document).ready(function () {
    var cfg = window.EkinerjaRekapConfig || {};
    var $unor = $('#filterUnor');
    var $periode = $('#filterPeriode');
    var $btnSync = $('#btnSyncRekap');
    var $emptyHint = $('#rekapEmptyHint');
    var table = null;

    /* ---------- Select2: Kantor / Unor (AJAX) ---------- */
    $unor.select2({
        placeholder: 'Pilih Kantor/Unor',
        width: '100%',
        allowClear: true,
        ajax: {
            url: cfg.urlUnorOptions,
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term || '' }; },
            processResults: function (data) { return { results: data.results || [] }; },
            cache: true
        }
    });

    /* ---------- Select2: Periode (AJAX, endpoint sama dengan frontend publik) ---------- */
    $periode.select2({
        placeholder: 'Pilih Periode',
        width: '100%',
        allowClear: true,
        ajax: {
            url: cfg.urlPeriodeOptions,
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term || '' }; },
            processResults: function (data) { return { results: data.results || [] }; },
            cache: true
        }
    });

    function toggleSyncButton() {
        $btnSync.prop('disabled', !($unor.val() && $periode.val()));
    }
    $unor.on('change', toggleSyncButton);
    $periode.on('change', toggleSyncButton);

    /* ---------- Init / reload DataTable server-side ---------- */
    function initTable() {
        $emptyHint.hide();

        if (table) {
            table.ajax.reload();
            return;
        }

        table = $('#datatableRekap').DataTable({
            searchDelay: 600,
            responsive: true,
            lengthChange: true,
            searching: true,
            processing: true,
            serverSide: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            ajax: {
                url: cfg.urlDatatable,
                data: function (d) {
                    d.unor_id = $unor.val();
                    d.periode_id = $periode.val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'nama', name: 'nama', defaultContent: '-' },
                { data: 'nip', name: 'nip', defaultContent: '-' },
                { data: 'skp_unor', name: 'skp_unor', defaultContent: '-' },
                { data: 'periode_skp', name: 'periode_skp', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'hasil_kerja_badge', name: 'hasil_kerja', orderable: false, searchable: false, className: 'text-center' },
                { data: 'perilaku_kerja_badge', name: 'perilaku_kerja', orderable: false, searchable: false, className: 'text-center' },
                { data: 'hasil_akhir_badge', name: 'hasil_akhir', orderable: false, searchable: false, className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ]
        });
    }

    /* ---------- Submit filter ---------- */
    $('#formFilterRekap').on('submit', function (e) {
        e.preventDefault();

        if (!$unor.val() || !$periode.val()) {
            swal('Filter belum lengkap', 'Silakan pilih Kantor/Unor dan Periode terlebih dahulu.', 'warning');
            return;
        }

        initTable();
    });

    /* ---------- Sinkronisasi manual ---------- */
    $btnSync.on('click', function () {
        if (!$unor.val() || !$periode.val()) return;

        swal({
            title: 'Sinkronisasi Data?',
            text: 'Sistem akan menarik ulang data penilaian dari BKN untuk Kantor/Unor & Periode terpilih.',
            icon: 'warning',
            buttons: ['Batal', 'Ya, Sinkronkan']
        }).then(function (confirmed) {
            if (!confirmed) return;

            var originalHtml = $btnSync.html();
            $btnSync.prop('disabled', true).html('<span class="fa fa-spinner fa-spin"></span> Menyinkronkan...');

            $.post(cfg.urlSync, {
                _token: cfg.csrfToken,
                unor_id: $unor.val(),
                periode_id: $periode.val()
            }).done(function (res) {
                swal('Selesai', res.message || 'Sinkronisasi selesai.', res.status === 'success' ? 'success' : 'error');
                if (table) table.ajax.reload(null, false);
            }).fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Sinkronisasi gagal, silakan coba lagi.';
                swal('Gagal', msg, 'error');
            }).always(function () {
                $btnSync.prop('disabled', false).html(originalHtml);
                toggleSyncButton();
            });
        });
    });
});