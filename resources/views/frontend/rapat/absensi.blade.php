<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir - {{ $agenda->nama }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <style>
        body { background: #000000; min-height:100vh; font-family:'Segoe UI',sans-serif; margin: 0; }
        #particles-js { position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0; }
        .container { position: relative; z-index: 1; }
        .card-main { border:none; border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.15); overflow:hidden; }
        .card-header-custom { background:linear-gradient(135deg,#29b6f6,#03a9f4); color:#fff; padding:24px; text-align:center; }
        .card-header-custom h4 { margin:0; font-weight:700; }
        .card-header-custom p { margin:5px 0 0; opacity:.85; font-size:14px; }
        .info-badge { display:inline-block; background:rgba(255,255,255,.2); padding:4px 12px; border-radius:20px; font-size:12px; margin:3px; }
        .signature-pad { border:2px solid #dee2e6; border-radius:8px; cursor:crosshair; background:#fff; touch-action:none; }
        .btn-primary-custom { background:linear-gradient(135deg,#29b6f6,#03a9f4); border:none; padding:12px 30px; border-radius:10px; font-weight:600; }
        .btn-primary-custom:hover { transform:translateY(-2px); box-shadow:0 5px 15px rgba(41,182,246,.4); }
        .alert-custom { border-radius:12px; border:none; }
        .peserta-table th { background:#f8f9fa; font-size:13px; }
        .peserta-table td { font-size:13px; }
    </style>
</head>
<body>
<div id="particles-js"></div>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-main">
                <div class="card-header-custom">
                    <h4><i class="fa fa-users"></i> Daftar Hadir Online</h4>
                    <p>{{ $agenda->nama }}</p>
                    <div class="mt-2">
                        <span class="info-badge"><i class="fa fa-calendar"></i> {{ $agenda->tanggal->format('d/m/Y') }}</span>
                        <span class="info-badge"><i class="fa fa-clock-o"></i> {{ substr($agenda->jam_mulai,0,5) }} - {{ substr($agenda->jam_selesai,0,5) }}</span>
                        <span class="info-badge"><i class="fa fa-map-marker"></i> {{ $agenda->tempat }}</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(!$allowed)
                    <div class="alert alert-warning alert-custom text-center py-4">
                        <i class="fa fa-exclamation-triangle fa-3x mb-3 d-block"></i>
                        <h5>{{ $message }}</h5>
                    </div>
                    @else
                    {{-- FORM KEHADIRAN --}}
                    <form id="form-absensi">
                        <h5 class="mb-3"><i class="fa fa-edit"></i> Isi Data Kehadiran</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP</label>
                            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP (jika ada)">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Jabatan">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Instansi</label>
                                <input type="text" name="instansi" class="form-control" placeholder="Nama instansi">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">No. HP</label>
                            <input type="tel" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>

                        {{-- TANDA TANGAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tanda Tangan <span class="text-danger">*</span></label>
                            <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
                            <input type="hidden" name="tanda_tangan" id="tanda_tangan">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-sig"><i class="fa fa-eraser"></i> Hapus</button>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-custom btn-lg text-white" id="btn-submit">
                                <i class="fa fa-check-circle"></i> Rekam Kehadiran
                            </button>
                        </div>
                    </form>

                    <div id="success-message" style="display:none;" class="text-center py-4">
                        <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                        <h4 class="text-success fw-bold">Kehadiran Berhasil Dicatat!</h4>
                        <p class="text-muted mb-4">Terima kasih atas kehadiran Anda.</p>
                        
                        <div id="materi-container" class="mt-4 text-start" style="display:none;">
                            <hr class="my-4">
                            <h5 class="mb-3 text-center fw-bold text-dark"><i class="fa fa-book text-primary"></i> Bahan / Materi Rapat</h5>
                            <div id="materi-list" class="mt-3">
                                <!-- List materi akan ditambahkan secara dinamis -->
                            </div>
                        </div>
                    </div>

                    {{-- DAFTAR YANG SUDAH HADIR --}}
                    @if(isset($pesertaHadir) && $pesertaHadir->count() > 0)
                    <hr>
                    <h6><i class="fa fa-list"></i> Peserta yang Sudah Hadir ({{ $pesertaHadir->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm peserta-table">
                            <thead><tr><th>No</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Waktu</th></tr></thead>
                            <tbody>
                            @foreach($pesertaHadir as $i => $p)
                            <tr><td>{{ $i+1 }}</td><td>{{ $p->nama }}</td><td>{{ $p->nip ?? '-' }}</td><td>{{ $p->jabatan ?? '-' }}</td><td>{{ $p->waktu_hadir ? $p->waktu_hadir->format('H:i') : '-' }}</td></tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($allowed)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Signature Pad
    var canvas = document.getElementById('signature-pad');
    var ctx = canvas.getContext('2d');
    var drawing = false;

    // Responsive canvas
    function resizeCanvas() {
        var rect = canvas.parentElement.getBoundingClientRect();
        canvas.width = Math.min(rect.width - 4, 500);
        canvas.height = 200;
        ctx.strokeStyle = '#333'; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    function getPos(e) {
        var rect = canvas.getBoundingClientRect();
        var touch = e.touches ? e.touches[0] : e;
        return { x: touch.clientX - rect.left, y: touch.clientY - rect.top };
    }

    canvas.addEventListener('mousedown', function(e) { drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove', function(e) { if(!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup', function() { drawing = false; });
    canvas.addEventListener('mouseleave', function() { drawing = false; });
    canvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; ctx.beginPath(); var p = getPos(e); ctx.moveTo(p.x, p.y); }, {passive:false});
    canvas.addEventListener('touchmove', function(e) { e.preventDefault(); if(!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, {passive:false});
    canvas.addEventListener('touchend', function() { drawing = false; });

    document.getElementById('btn-clear-sig').addEventListener('click', function() { ctx.clearRect(0, 0, canvas.width, canvas.height); });

    // Form Submit
    document.getElementById('form-absensi').addEventListener('submit', function(e) {
        e.preventDefault();

        // Check signature
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var hasSignature = false;
        for (var i = 3; i < imageData.data.length; i += 4) { if (imageData.data[i] > 0) { hasSignature = true; break; } }
        if (!hasSignature) { 
            swal('Perhatian', 'Silakan tanda tangan terlebih dahulu.', 'warning'); 
            return; 
        }

        document.getElementById('tanda_tangan').value = canvas.toDataURL('image/png');

        var formData = new FormData(this);
        var btn = document.getElementById('btn-submit');
        btn.disabled = true; btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';

        fetch("{{ route('rapat.absensi.store', $agenda->barcode_token) }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.status) {
                document.getElementById('form-absensi').style.display = 'none';
                document.getElementById('success-message').style.display = 'block';
                
                if (res.materi && res.materi.length > 0) {
                    var container = document.getElementById('materi-container');
                    var list = document.getElementById('materi-list');
                    list.innerHTML = '';
                    
                    res.materi.forEach(function(item) {
                        var ext = item.name.split('.').pop().toLowerCase();
                        var iconClass = 'fa-file-text-o text-secondary';
                        if (ext === 'pdf') iconClass = 'fa-file-pdf-o text-danger';
                        else if (ext === 'doc' || ext === 'docx') iconClass = 'fa-file-word-o text-primary';
                        else if (ext === 'xls' || ext === 'xlsx') iconClass = 'fa-file-excel-o text-success';
                        else if (ext === 'ppt' || ext === 'pptx') iconClass = 'fa-file-powerpoint-o text-warning';
                        else if (ext === 'jpg' || ext === 'jpeg' || ext === 'png') iconClass = 'fa-file-image-o text-info';
                        
                        var html = 
                            '<div class="d-flex align-items-center justify-content-between p-3 mb-2 border rounded bg-light" style="border-color: #dee2e6 !important;">' +
                                '<div class="d-flex align-items-center gap-3" style="min-width: 0;">' +
                                    '<div class="bg-white border rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px; flex-shrink: 0;">' +
                                        '<i class="fa ' + iconClass + ' fa-lg"></i>' +
                                    '</div>' +
                                    '<div class="text-start" style="min-width: 0;">' +
                                        '<h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 14px; max-width: 250px;" title="' + item.name + '">' + item.name + '</h6>' +
                                        '<small class="text-muted">Bahan / Materi Rapat</small>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="d-flex gap-2 flex-shrink-0">' +
                                    '<a href="' + item.link_stream + '" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">' +
                                        '<i class="fa fa-eye"></i> Lihat' +
                                    '</a>' +
                                    '<a href="' + item.link_download + '" class="btn btn-sm btn-success rounded-pill px-3" download>' +
                                        '<i class="fa fa-download"></i> Unduh' +
                                    '</a>' +
                                '</div>' +
                            '</div>';
                        list.insertAdjacentHTML('beforeend', html);
                    });
                    
                    container.style.display = 'block';
                } else {
                    var container = document.getElementById('materi-container');
                    var list = document.getElementById('materi-list');
                    list.innerHTML = 
                        '<div class="alert alert-info text-center py-3" style="border-radius: 8px; border: none; background: #e0f2fe; color: #0369a1;">' +
                            '<i class="fa fa-info-circle fa-2x mb-2 d-block"></i>' +
                            '<span class="d-block" style="font-size: 14px;">Belum ada bahan atau materi rapat yang diunggah untuk agenda ini.</span>' +
                        '</div>';
                    container.style.display = 'block';
                }
            } else {
                swal('Perhatian', res.message || 'Gagal menyimpan kehadiran.', 'warning');
                btn.disabled = false; btn.innerHTML = '<i class="fa fa-check-circle"></i> Rekam Kehadiran';
            }
        })
        .catch(function() {
            swal('Error', 'Terjadi kesalahan. Silakan coba lagi.', 'error');
            btn.disabled = false; btn.innerHTML = '<i class="fa fa-check-circle"></i> Rekam Kehadiran';
        });
    });
});
    document.addEventListener('DOMContentLoaded', function () {
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#00bfff' },
                shape: {
                    type: 'star',
                    stroke: { width: 0, color: '#000000' },
                    polygon: { nb_sides: 5 }
                },
                opacity: { value: 0.5, random: true, anim: { enable: false } },
                size: { value: 3, random: true, anim: { enable: false } },
                line_linked: { enable: true, distance: 150, color: '#00bfff', opacity: 0.4, width: 1 },
                move: { enable: true, speed: 3, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'grab' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                },
                modes: {
                    grab: { distance: 140, line_linked: { opacity: 1 } },
                    bubble: { distance: 400, size: 40, duration: 2, opacity: 0.8, speed: 3 },
                    repulse: { distance: 200, duration: 0.4 },
                    push: { particles_nb: 4 },
                    remove: { particles_nb: 2 }
                }
            },
            retina_detect: true
        });
    });
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', function () {
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#00bfff' },
                shape: {
                    type: 'star',
                    stroke: { width: 0, color: '#000000' },
                    polygon: { nb_sides: 5 }
                },
                opacity: { value: 0.5, random: true, anim: { enable: false } },
                size: { value: 3, random: true, anim: { enable: false } },
                line_linked: { enable: true, distance: 150, color: '#00bfff', opacity: 0.4, width: 1 },
                move: { enable: true, speed: 3, direction: 'none', random: false, straight: false, out_mode: 'out', bounce: false }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'grab' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                },
                modes: {
                    grab: { distance: 140, line_linked: { opacity: 1 } },
                    bubble: { distance: 400, size: 40, duration: 2, opacity: 0.8, speed: 3 },
                    repulse: { distance: 200, duration: 0.4 },
                    push: { particles_nb: 4 },
                    remove: { particles_nb: 2 }
                }
            },
            retina_detect: true
        });
    });
</script>
@endif
</body>
</html>
