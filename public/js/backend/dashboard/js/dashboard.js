/**
 * Dashboard Chart Scripts
 * /public/js/backend/dashboard/js/dashboard.js
 *
 * Data-binding: semua variabel PHP dikirim ke window.DASH sebelum
 * file ini diload (lihat blade: @push('js') script block).
 */

/* ── Palette & Defaults ──────────────────────────────────────────────────── */
const PALETTE = [
    '#6366f1','#06b6d4','#10b981','#f59e0b','#ef4444',
    '#8b5cf6','#ec4899','#14b8a6','#f97316','#3b82f6',
    '#a855f7','#84cc16','#22d3ee','#fb923c','#e879f9',
];

Chart.defaults.font.family = "'Nunito', 'Inter', sans-serif";
Chart.defaults.font.size   = 12;

/* ── Utility helpers ─────────────────────────────────────────────────────── */
function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1,3), 16);
    const g = parseInt(hex.slice(3,5), 16);
    const b = parseInt(hex.slice(5,7), 16);
    return `rgba(${r},${g},${b},${alpha})`;
}

/**
 * Hitung total dari array data dan buat label legend
 * dengan format "Label — N (XX%)"
 */
function buildLegendLabels(dataArr) {
    const total = dataArr.reduce((sum, d) => sum + d.total, 0);
    return dataArr.map(d => {
        const pct = total > 0 ? ((d.total / total) * 100).toFixed(1) : '0.0';
        return `${d.label} — ${d.total} (${pct}%)`;
    });
}

/**
 * Ganti canvas dengan pesan kosong
 */
function showEmpty(canvasId, message = 'Belum ada data') {
    const el = document.getElementById(canvasId);
    if (!el) return;
    el.parentElement.innerHTML =
        `<div class="chart-empty"><i class="fa fa-bar-chart"></i>${message}</div>`;
}

function initChartWhenVisible(canvasId, renderer, delay = 120) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const card = canvas.closest('.chart-card');
    if (!card) return;

    card.style.opacity = '0';
    card.style.transform = 'translateY(14px)';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';

    const reveal = () => {
        window.setTimeout(() => {
            renderer();
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, delay);
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    reveal();
                    obs.disconnect();
                }
            });
        }, { threshold: 0.2 });

        observer.observe(card);
    } else {
        reveal();
    }
}

const DEFAULT_ANIMATION = {
    duration: 900,
    easing: 'easeOutQuart',
};

/* ── Ambil data dari window scope ────────────────────────────────────────── */
const {
    aplikasiByJenis,
    aplikasiByStatus,
    aplikasiByTahun,
    aplikasiByPengguna,
    aplikasiByUnor,
} = window.DASH || {};

/* ════════════════════════════════════════════════════════════════════════════
   1. DONUT — Aplikasi berdasarkan Jenis
   ════════════════════════════════════════════════════════════════════════════ */
initChartWhenVisible('chartJenis', function initChartJenis() {
    const data = aplikasiByJenis || [];
    if (!data.length) { showEmpty('chartJenis'); return; }

    new Chart(document.getElementById('chartJenis'), {
        type: 'doughnut',
        data: {
            labels: buildLegendLabels(data),
            datasets: [{
                data: data.map(d => d.total),
                backgroundColor: PALETTE.slice(0, data.length),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: DEFAULT_ANIMATION,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 12,
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : '0.0';
                            return ` ${data[ctx.dataIndex].label}: ${ctx.raw} aplikasi (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
});

/* ════════════════════════════════════════════════════════════════════════════
   2. PIE — Aplikasi berdasarkan Status
   ════════════════════════════════════════════════════════════════════════════ */
initChartWhenVisible('chartStatus', function initChartStatus() {
    const data = aplikasiByStatus || [];
    if (!data.length) { showEmpty('chartStatus'); return; }

    const STATUS_COLOR = {
        aktif:       '#10b981',
        maintenance: '#f59e0b',
        suspend:     '#ef4444',
    };

    new Chart(document.getElementById('chartStatus'), {
        type: 'pie',
        data: {
            labels: buildLegendLabels(data.map(d => ({...d, label: d.label.toUpperCase()}))),
            datasets: [{
                data: data.map(d => d.total),
                backgroundColor: data.map((d, i) =>
                    STATUS_COLOR[d.label?.toLowerCase()] || PALETTE[i]),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: DEFAULT_ANIMATION,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 12,
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : '0.0';
                            return ` ${data[ctx.dataIndex].label.toUpperCase()}: ${ctx.raw} aplikasi (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
});

/* ════════════════════════════════════════════════════════════════════════════
   3. LINE — Aplikasi berdasarkan Tahun
   ════════════════════════════════════════════════════════════════════════════ */
initChartWhenVisible('chartTahun', function initChartTahun() {
    const data = aplikasiByTahun || [];
    if (!data.length) { showEmpty('chartTahun'); return; }

    new Chart(document.getElementById('chartTahun'), {
        type: 'line',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                label: 'Jumlah Aplikasi',
                data: data.map(d => d.total),
                borderColor: '#6366f1',
                backgroundColor: hexToRgba('#6366f1', 0.12),
                pointBackgroundColor: '#6366f1',
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
                borderWidth: 2.5,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: DEFAULT_ANIMATION,
            animations: {
                tension: {
                    duration: 900,
                    easing: 'easeOutCubic',
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.raw} aplikasi`,
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6b7280' } },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#6b7280' },
                    grid: { color: '#f3f4f6' },
                },
            },
        },
    });
});

/* ════════════════════════════════════════════════════════════════════════════
   4. HORIZONTAL BAR — Aplikasi berdasarkan Pengguna
      (Donut variant: tambah legend dengan jumlah & persentase)
   ════════════════════════════════════════════════════════════════════════════ */
initChartWhenVisible('chartPengguna', function initChartPengguna() {
    const data = aplikasiByPengguna || [];
    if (!data.length) { showEmpty('chartPengguna'); return; }

    new Chart(document.getElementById('chartPengguna'), {
        type: 'doughnut',
        data: {
            labels: buildLegendLabels(data),
            datasets: [{
                data: data.map(d => d.total),
                backgroundColor: data.map((d, i) => PALETTE[i % PALETTE.length]),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: DEFAULT_ANIMATION,
            cutout: '58%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 10,
                        font: { size: 11 },
                    },
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : '0.0';
                            return ` ${data[ctx.dataIndex].label}: ${ctx.raw} aplikasi (${pct}%)`;
                        },
                    },
                },
            },
        },
    });
});

/* ════════════════════════════════════════════════════════════════════════════
   5. HORIZONTAL BAR — Aplikasi berdasarkan Unor (OPD)
      Diubah dari vertical bar → horizontal bar agar nama OPD terbaca jelas
   ════════════════════════════════════════════════════════════════════════════ */
initChartWhenVisible('chartUnor', function initChartUnor() {
    const data = aplikasiByUnor || [];
    if (!data.length) { showEmpty('chartUnor'); return; }

    // Urutkan dari terbesar ke terkecil agar bar chart lebih informatif
    const sorted = [...data].sort((a, b) => b.total - a.total);

    new Chart(document.getElementById('chartUnor'), {
        type: 'bar',
        data: {
            labels: sorted.map(d => d.label),
            datasets: [{
                label: 'Jumlah Aplikasi',
                data: sorted.map(d => d.total),
                backgroundColor: sorted.map((d, i) => hexToRgba(PALETTE[i % PALETTE.length], 0.82)),
                borderColor:     sorted.map((d, i) => PALETTE[i % PALETTE.length]),
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y',          // ← horizontal bar
            responsive: true,
            maintainAspectRatio: false,
            animation: DEFAULT_ANIMATION,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        title: ctx  => sorted[ctx[0].dataIndex]?.label ?? ctx[0].label,
                        label: ctx  => ` ${ctx.raw} aplikasi`,
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#6b7280' },
                    grid: { color: '#f3f4f6' },
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        color: '#374151',
                        font: { size: 11.5 },
                        // Potong nama OPD maks 40 karakter supaya tidak tumpang tindih
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            return label.length > 42 ? label.slice(0, 40) + '…' : label;
                        },
                    },
                },
            },
            layout: { padding: { right: 12 } },
        },
    });
});
