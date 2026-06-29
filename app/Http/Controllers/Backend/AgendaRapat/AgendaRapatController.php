<?php

namespace App\Http\Controllers\Backend\AgendaRapat;

use App\Http\Controllers\Controller;
use App\Models\AgendaRapat;
use App\Models\RapatVerifikasi;
use App\Models\RapatNotulen;
use App\Models\Pegawai;
use App\Models\DokumenTte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Facades\Verification;
use App\Services\BsreSignService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AgendaRapatController extends Controller
{
    // =====================================================
    // INDEX
    // =====================================================
    public function index()
    {
        return view($this->view . '.index');
    }

    // =====================================================
    // DATA (Datatable)
    // =====================================================
    public function data(Request $request)
    {
        $user = $request->user();

        $query = AgendaRapat::with('user');

        // Level 3 = user biasa: hanya lihat data sendiri
        if ($user->level_id == 3) {
            $query->where('user_id', $user->id);
        }

        $data = $query->latest()->get();

        return datatables()->of($data)
            ->editColumn('tanggal', function ($data) {
                return $data->tanggal ? $data->tanggal->format('d/m/Y') : '-';
            })
            ->addColumn('waktu', function ($data) {
                return substr($data->jam_mulai, 0, 5) . ' - ' . substr($data->jam_selesai, 0, 5);
            })
            ->editColumn('status', function ($data) {
                return Verification::renderStatusBadge($data->status);
            })
            ->addColumn('action', function ($data) use ($user) {
                $button = '';

                if ($user->read) {
                    $button .= '<a href="' . url(config('master.app.url.backend') . '/' . $this->url . '/' . $data->id) . '" class="btn btn-sm btn-outline" title="Detail"><i class="fa fa-eye text-info"></i></a>';
                }

                if ($user->update && in_array($data->status, ['DRAFT', 'REVISI'])) {
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Edit" data-action="edit" data-url="' . $this->url . '" data-id="' . $data->id . '" title="Edit"><i class="fa fa-edit text-warning"></i></button>';
                }

                if ($user->delete && in_array($data->status, ['DRAFT', 'REVISI'])) {
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Delete" data-action="delete" data-url="' . $this->url . '" data-id="' . $data->id . '" title="Delete"><i class="fa fa-trash text-danger"></i></button>';
                }

                return "<div class='btn-group'>" . $button . "</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])
            ->make();
    }

    // =====================================================
    // CREATE
    // =====================================================
    public function create()
    {
        $pegawais = Pegawai::all();
        return view($this->view . '.create', compact('pegawais'));
    }

    // =====================================================
    // STORE
    // =====================================================
    public function store(Request $request)
    {
        $request->validate([
            'nama'                  => 'required|string|max:255',
            'tanggal'               => 'required|date',
            'jam_mulai'             => 'required',
            'jam_selesai'           => 'required',
            'tipe_rapat'            => 'required|in:online,offline',
            'tempat'                => 'required_if:tipe_rapat,offline|nullable|string|max:255',
            'zoom_meeting_id'       => 'required_if:tipe_rapat,online|nullable|string|max:255',
            'zoom_password'         => 'required_if:tipe_rapat,online|nullable|string|max:255',
            'acara'                 => 'required',
            'jenis_tujuan_surat'    => 'required|in:tunggal,lampiran',
            'surat_tujuan'          => 'required_if:jenis_tujuan_surat,tunggal|nullable|string',
            'surat_tujuan_lampiran' => 'required_if:jenis_tujuan_surat,lampiran|nullable|string',
            'catatan'               => 'nullable|string',
        ]);

        // Validasi konflik jadwal: lokasi + tanggal + waktu yang overlap (hanya jika tipe offline)
        if ($request->tipe_rapat === 'offline') {
            $konflik = AgendaRapat::where('tipe_rapat', 'offline')
                ->where('tempat', $request->tempat)
                ->where('tanggal', $request->tanggal)
                ->where(function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where('jam_mulai', '<', $request->jam_selesai)
                            ->where('jam_selesai', '>', $request->jam_mulai);
                    });
                })
                ->whereNotIn('status', ['DITOLAK'])
                ->first();

            if ($konflik) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak bisa membuat agenda rapat karena pada waktu dan lokasi ini sudah ada agenda rapat lain yaitu: "' . $konflik->nama . '" (' . substr($konflik->jam_mulai, 0, 5) . ' - ' . substr($konflik->jam_selesai, 0, 5) . ')',
                ], 422);
            }
        }

        $agendaRapat = AgendaRapat::create([
            'nama'                  => $request->nama,
            'tanggal'               => $request->tanggal,
            'jam_mulai'             => $request->jam_mulai,
            'jam_selesai'           => $request->jam_selesai,
            'tipe_rapat'            => $request->tipe_rapat,
            'tempat'                => $request->tipe_rapat === 'online' ? 'Zoom Meeting' : $request->tempat,
            'zoom_meeting_id'       => $request->zoom_meeting_id,
            'zoom_password'         => $request->zoom_password,
            'acara'                 => $request->acara,
            'deskripsi'             => $request->deskripsi,
            'catatan'               => $request->catatan,
            'status'                => 'DRAFT',
            'dasar_dari'            => $request->dasar_dari,
            'dasar_no'              => $request->dasar_no,
            'dasar_tgl'             => $request->dasar_tgl,
            'dasar_hal'             => $request->dasar_hal,
            'surat_nomor'           => $request->surat_nomor,
            'surat_sifat'           => $request->surat_sifat,
            'surat_lampiran'        => $request->surat_lampiran,
            'surat_hal'             => $request->surat_hal,
            'jenis_tujuan_surat'    => $request->jenis_tujuan_surat,
            'surat_tujuan'          => $request->jenis_tujuan_surat === 'lampiran' ? 'Daftar terlampir' : $request->surat_tujuan,
            'surat_tujuan_lampiran' => $request->surat_tujuan_lampiran,
            'pegawai_id'            => $request->pegawai_id,
            'jenis_tanda_tangan'    => $request->jenis_tanda_tangan ?? 'manual',
            'barcode_token'         => Str::random(32),
            'user_id'               => $request->user()->id,
        ]);

        // Upload berkas dasar surat (multiple)
        if ($request->hasFile('dasar_surat')) {
            $disk = config('filesystems.default');
            $files = $request->file('dasar_surat');
            if (!is_array($files)) $files = [$files];

            foreach ($files as $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) continue;

                // Validasi tipe dan ukuran
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) continue;
                if ($file->getSize() > 2 * 1024 * 1024) continue;

                $path = Storage::disk($disk)->putFile('agenda-rapat/dasar-surat', $file);
                if ($path) {
                    $agendaRapat->file()->create([
                        'alias' => 'dasar_surat',
                        'data'  => [
                            'name'   => $file->getClientOriginalName(),
                            'disk'   => $disk,
                            'target' => $path,
                        ],
                    ]);
                }
            }
        }

        if ($agendaRapat) {
            return response()->json(['status' => true, 'message' => 'Data agenda rapat berhasil disimpan sebagai DRAFT']);
        }

        return response()->json(['status' => false, 'message' => 'Data gagal disimpan']);
    }

    // =====================================================
    // SHOW (Detail + semua data pendukung)
    // =====================================================
    public function show($id)
    {
        $data = AgendaRapat::with('user', 'verifikasi.user', 'peserta', 'notulen')->findOrFail($id);

        $histori_verifikasi = collect();
        if ($data->verifikasi->count() > 0) {
            $histori_verifikasi = $data->verifikasi->map(function ($v) {
                return [
                    'id'             => $v->id,
                    'status'         => $v->status,
                    'status_badge'   => Verification::getStatusBadgeClass($v->status),
                    'status_color'   => Verification::getStatusColor($v->status),
                    'status_icon'    => Verification::getStatusIcon($v->status),
                    'user_name'      => $v->user->name ?? 'N/A',
                    'user_level'     => $v->user->level->name ?? null,
                    'catatan'        => $v->catatan,
                    'created_at'     => $v->created_at,
                    'formatted_date' => $v->created_at->format('d M Y H:i'),
                ];
            });
        }

        // Ambil files per alias
        $dasar_surat   = $data->getfilesbyalias('dasar_surat');
        $dokumentasi   = $data->getfilesbyalias('rapat_dokumentasi');
        $materi        = $data->getfilesbyalias('rapat_materi');

        $user = auth()->user();
        $pegawais = Pegawai::all();

        // TTE status per jenis dokumen
        $tteUndangan       = $data->getDokumenTteByJenis('undangan');
        $tteDaftarHadir    = $data->getDokumenTteByJenis('daftar_hadir');
        $tteNotulenNotulis = $data->getDokumenTteByJenis('notulen_notulis');
        $tteNotulenPimpinan= $data->getDokumenTteByJenis('notulen_pimpinan');

        // Cek apakah user saat ini adalah pegawai yang ditunjuk
        $pegawai_login = Pegawai::where('user_id', $user->id)->first();
        $isSignatory = $data->pegawai && $data->pegawai->user_id === $user->id;

        return view($this->view . '.show', compact(
            'data',
            'histori_verifikasi',
            'dasar_surat',
            'dokumentasi',
            'materi',
            'user',
            'pegawais',
            'pegawai_login',
            'tteUndangan',
            'tteDaftarHadir',
            'tteNotulenNotulis',
            'tteNotulenPimpinan',
            'isSignatory'
        ));
    }

    // =====================================================
    // EDIT
    // =====================================================
    public function edit($id)
    {
        $data = AgendaRapat::findOrFail($id);
        $dasar_surat = $data->getfilesbyalias('dasar_surat');
        $pegawais = Pegawai::all();

        return view($this->view . '.edit', compact('data', 'dasar_surat', 'pegawais'));
    }

    // =====================================================
    // UPDATE
    // =====================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'                  => 'required|string|max:255',
            'tanggal'               => 'required|date',
            'jam_mulai'             => 'required',
            'jam_selesai'           => 'required',
            'tipe_rapat'            => 'required|in:online,offline',
            'tempat'                => 'required_if:tipe_rapat,offline|nullable|string|max:255',
            'zoom_meeting_id'       => 'required_if:tipe_rapat,online|nullable|string|max:255',
            'zoom_password'         => 'required_if:tipe_rapat,online|nullable|string|max:255',
            'acara'                 => 'required',
            'jenis_tujuan_surat'    => 'required|in:tunggal,lampiran',
            'surat_tujuan'          => 'required_if:jenis_tujuan_surat,tunggal|nullable|string',
            'surat_tujuan_lampiran' => 'required_if:jenis_tujuan_surat,lampiran|nullable|string',
            'catatan'               => 'nullable|string',
        ]);

        $agendaRapat = AgendaRapat::findOrFail($id);

        // Validasi konflik jadwal (exclude diri sendiri, hanya jika offline)
        if ($request->tipe_rapat === 'offline') {
            $konflik = AgendaRapat::where('tipe_rapat', 'offline')
                ->where('tempat', $request->tempat)
                ->where('tanggal', $request->tanggal)
                ->where('id', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->where(function ($sub) use ($request) {
                        $sub->where('jam_mulai', '<', $request->jam_selesai)
                            ->where('jam_selesai', '>', $request->jam_mulai);
                    });
                })
                ->whereNotIn('status', ['DITOLAK'])
                ->first();

            if ($konflik) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak bisa menggunakan waktu dan lokasi ini karena sudah ada agenda rapat lain yaitu: "' . $konflik->nama . '" (' . substr($konflik->jam_mulai, 0, 5) . ' - ' . substr($konflik->jam_selesai, 0, 5) . ')',
                ], 422);
            }
        }

        $agendaRapat->update([
            'nama'                  => $request->nama,
            'tanggal'               => $request->tanggal,
            'jam_mulai'             => $request->jam_mulai,
            'jam_selesai'           => $request->jam_selesai,
            'tipe_rapat'            => $request->tipe_rapat,
            'tempat'                => $request->tipe_rapat === 'online' ? 'Zoom Meeting' : $request->tempat,
            'zoom_meeting_id'       => $request->zoom_meeting_id,
            'zoom_password'         => $request->zoom_password,
            'acara'                 => $request->acara,
            'deskripsi'             => $request->deskripsi,
            'catatan'               => $request->catatan,
            'dasar_dari'            => $request->dasar_dari,
            'dasar_no'              => $request->dasar_no,
            'dasar_tgl'             => $request->dasar_tgl,
            'dasar_hal'             => $request->dasar_hal,
            'surat_nomor'           => $request->surat_nomor,
            'surat_sifat'           => $request->surat_sifat,
            'surat_lampiran'        => $request->surat_lampiran,
            'surat_hal'             => $request->surat_hal,
            'jenis_tujuan_surat'    => $request->jenis_tujuan_surat,
            'surat_tujuan'          => $request->jenis_tujuan_surat === 'lampiran' ? 'Daftar terlampir' : $request->surat_tujuan,
            'surat_tujuan_lampiran' => $request->surat_tujuan_lampiran,
            'pegawai_id'            => $request->pegawai_id,
            'jenis_tanda_tangan'    => $request->jenis_tanda_tangan ?? 'manual',
            'user_id'               => $request->user()->id,
        ]);

        // Upload berkas dasar surat baru (tambah, tidak replace)
        if ($request->hasFile('dasar_surat')) {
            $disk = config('filesystems.default');
            $files = $request->file('dasar_surat');
            if (!is_array($files)) $files = [$files];

            foreach ($files as $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) continue;
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) continue;
                if ($file->getSize() > 2 * 1024 * 1024) continue;

                $path = Storage::disk($disk)->putFile('agenda-rapat/dasar-surat', $file);
                if ($path) {
                    $agendaRapat->file()->create([
                        'alias' => 'dasar_surat',
                        'data'  => [
                            'name'   => $file->getClientOriginalName(),
                            'disk'   => $disk,
                            'target' => $path,
                        ],
                    ]);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Data agenda rapat berhasil diperbarui']);
    }

    // =====================================================
    // DELETE (confirm view)
    // =====================================================
    public function delete($id)
    {
        $data = AgendaRapat::find($id);
        return view($this->view . '.delete', compact('data'));
    }

    // =====================================================
    // DESTROY
    // =====================================================
    public function destroy($id)
    {
        $data = AgendaRapat::find($id);
        if ($data->delete()) {
            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
        }
        return response()->json(['status' => false, 'message' => 'Data gagal dihapus']);
    }

    // =====================================================
    // KIRIM (DRAFT/REVISI -> PENGAJUAN)
    // =====================================================
    public function kirim($id)
    {
        $agendaRapat = AgendaRapat::findOrFail($id);

        if (!in_array($agendaRapat->status, ['DRAFT', 'REVISI'])) {
            return response()->json(['status' => false, 'message' => 'Data bukan berstatus DRAFT atau REVISI']);
        }

        $agendaRapat->update(['status' => 'PENGAJUAN']);

        return response()->json(['status' => true, 'message' => 'Agenda rapat berhasil dikirim untuk verifikasi']);
    }

    // =====================================================
    // CHECK KONFLIK (AJAX)
    // =====================================================
    public function checkKonflik(Request $request)
    {
        $konflik = AgendaRapat::where('tempat', $request->tempat)
            ->where('tanggal', $request->tanggal)
            ->when($request->exclude_id, function ($q) use ($request) {
                $q->where('id', '!=', $request->exclude_id);
            })
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->whereNotIn('status', ['DITOLAK'])
            ->first();

        if ($konflik) {
            return response()->json([
                'konflik' => true,
                'message' => 'Pada waktu dan lokasi ini sudah ada agenda rapat: "' . $konflik->nama . '" (' . substr($konflik->jam_mulai, 0, 5) . ' - ' . substr($konflik->jam_selesai, 0, 5) . ')',
            ]);
        }

        return response()->json(['konflik' => false]);
    }

    // =====================================================
    // STORE NOTULEN
    // =====================================================
    public function storeNotulen(Request $request, $id)
    {
        $request->validate([
            'isi_notulen'       => 'required',
            'pimpinan_rapat_id' => 'required',
            'notulis_id'        => 'required',
        ]);

        $agendaRapat = AgendaRapat::findOrFail($id);
        $pimpinan = Pegawai::find($request->pimpinan_rapat_id);
        $notulis = Pegawai::find($request->notulis_id);

        if (!$pimpinan || !$notulis) {
            return response()->json(['status' => false, 'message' => 'Pegawai tidak ditemukan']);
        }

        $notulen = RapatNotulen::updateOrCreate(
            ['agenda_rapat_id' => $id],
            [
                'isi_notulen'       => $request->isi_notulen,
                'pimpinan_rapat_id' => $pimpinan->id,
                'pimpinan_rapat'    => $pimpinan->nama,
                'notulis_id'        => $notulis->id,
                'notulis'           => $notulis->nama,
                'hasil_rapat'       => $request->hasil_rapat,
                'user_id'           => $request->user()->id,
                'status'            => 'DRAFT',
                'catatan_revisi'    => null,
            ]
        );

        return response()->json(['status' => true, 'message' => 'Notulen rapat berhasil disimpan sebagai DRAFT']);
    }

    public function kirimNotulen($id)
    {
        $notulen = RapatNotulen::where('agenda_rapat_id', $id)->firstOrFail();
        
        if (!in_array($notulen->status, ['DRAFT', 'REVISI'])) {
            return response()->json(['status' => false, 'message' => 'Notulen bukan berstatus DRAFT/REVISI']);
        }

        $notulen->update(['status' => 'MENUNGGU_PERSETUJUAN']);
        return response()->json(['status' => true, 'message' => 'Notulen berhasil dikirim ke Pimpinan Rapat']);
    }

    public function setujuNotulen($id)
    {
        $notulen = RapatNotulen::where('agenda_rapat_id', $id)->firstOrFail();
        
        // Pastikan hanya pimpinan yang bisa setuju
        $pegawai_login = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai_login || $pegawai_login->id !== $notulen->pimpinan_rapat_id) {
            return response()->json(['status' => false, 'message' => 'Hanya Pimpinan Rapat yang berhak menyetujui notulen.'], 403);
        }

        $notulen->update(['status' => 'DISETUJUI', 'catatan_revisi' => null]);
        return response()->json(['status' => true, 'message' => 'Notulen telah disetujui. Sekarang bisa ditandatangani secara elektronik.']);
    }

    public function revisiNotulen(Request $request, $id)
    {
        $request->validate(['catatan' => 'required|string']);
        $notulen = RapatNotulen::where('agenda_rapat_id', $id)->firstOrFail();
        
        $pegawai_login = Pegawai::where('user_id', auth()->id())->first();
        if (!$pegawai_login || $pegawai_login->id !== $notulen->pimpinan_rapat_id) {
            return response()->json(['status' => false, 'message' => 'Hanya Pimpinan Rapat yang berhak memberikan revisi.'], 403);
        }

        $notulen->update(['status' => 'REVISI', 'catatan_revisi' => $request->catatan]);
        return response()->json(['status' => true, 'message' => 'Notulen dikembalikan untuk direvisi.']);
    }

    // =====================================================
    // STORE DOKUMENTASI (Multiple upload foto)
    // =====================================================
    public function storeDokumentasi(Request $request, $id)
    {
        $agendaRapat = AgendaRapat::findOrFail($id);
        $disk = config('filesystems.default');
        $uploaded = 0;

        if ($request->hasFile('dokumentasi')) {
            $files = $request->file('dokumentasi');
            if (!is_array($files)) $files = [$files];

            foreach ($files as $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) continue;

                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) continue;

                // Kompres jika lebih dari 2MB
                $fileContent = file_get_contents($file->getPathname());
                $sizeBytes = strlen($fileContent);

                if ($sizeBytes > 2 * 1024 * 1024) {
                    // Kompres gambar
                    $image = @imagecreatefromstring($fileContent);
                    if ($image) {
                        $tmpPath = tempnam(sys_get_temp_dir(), 'rapat_');
                        imagejpeg($image, $tmpPath, 70); // quality 70%
                        imagedestroy($image);
                        $path = Storage::disk($disk)->putFile('agenda-rapat/dokumentasi', new \Illuminate\Http\File($tmpPath));
                        @unlink($tmpPath);
                    } else {
                        continue;
                    }
                } else {
                    $path = Storage::disk($disk)->putFile('agenda-rapat/dokumentasi', $file);
                }

                if ($path) {
                    $agendaRapat->file()->create([
                        'alias' => 'rapat_dokumentasi',
                        'data'  => [
                            'name'   => $file->getClientOriginalName(),
                            'disk'   => $disk,
                            'target' => $path,
                        ],
                    ]);
                    $uploaded++;
                }
            }
        }

        return response()->json([
            'status'  => $uploaded > 0,
            'message' => $uploaded > 0 ? "$uploaded foto dokumentasi berhasil diupload" : 'Tidak ada file yang berhasil diupload',
        ]);
    }

    // =====================================================
    // STORE MATERI (Multiple upload dokumen)
    // =====================================================
    public function storeMateri(Request $request, $id)
    {
        $agendaRapat = AgendaRapat::findOrFail($id);
        $disk = config('filesystems.default');
        $uploaded = 0;

        if ($request->hasFile('materi')) {
            $files = $request->file('materi');
            if (!is_array($files)) $files = [$files];

            foreach ($files as $file) {
                if (!$file instanceof \Illuminate\Http\UploadedFile || !$file->isValid()) continue;

                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['doc', 'docx', 'pdf', 'ppt', 'pptx'])) continue;
                if ($file->getSize() > 2 * 1024 * 1024) continue;

                $path = Storage::disk($disk)->putFile('agenda-rapat/materi', $file);
                if ($path) {
                    $agendaRapat->file()->create([
                        'alias' => 'rapat_materi',
                        'data'  => [
                            'name'   => $file->getClientOriginalName(),
                            'disk'   => $disk,
                            'target' => $path,
                        ],
                    ]);
                    $uploaded++;
                }
            }
        }

        return response()->json([
            'status'  => $uploaded > 0,
            'message' => $uploaded > 0 ? "$uploaded materi rapat berhasil diupload" : 'Tidak ada file yang berhasil diupload',
        ]);
    }

    // =====================================================
    // EXPORT UNDANGAN RAPAT (PDF)
    // =====================================================
    public function exportUndangan($id)
    {
        $data = AgendaRapat::with('user', 'pegawai')->findOrFail($id);

        if ($data->status !== 'DITERIMA') {
            return response()->json(['status' => false, 'message' => 'Undangan hanya bisa didownload jika status DITERIMA'], 403);
        }

        if ($data->jenis_tanda_tangan === 'elektronik') {
            $dokumen = DokumenTte::where('agenda_rapat_id', $id)
                ->where('jenis_dokumen', 'undangan')
                ->where('status', 'signed')
                ->first();

            if ($dokumen) {
                $file = $dokumen->file()->where('alias', 'dokumen_tte_signed')->first();
                if ($file && $file->exists) {
                    return redirect()->to($file->link_download);
                }
            }

            return response()->json(['status' => false, 'message' => 'Undangan dengan TTE belum berhasil ditandatangani. Silakan hubungi administrator.'], 404);
        }

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadView('backend.agenda-rapat.pdf.undangan', compact('data'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Undangan_Rapat_' . Str::slug($data->nama) . '.pdf');
    }

    // =====================================================
    // EXPORT NOTULEN (PDF)
    // =====================================================
    public function exportNotulen($id)
    {
        $data = AgendaRapat::with('notulen', 'peserta', 'user', 'pegawai')->findOrFail($id);

        if (!$data->notulen) {
            return response()->json(['status' => false, 'message' => 'Notulen belum dibuat'], 404);
        }

        $pimpinan = Pegawai::where('nama', $data->notulen->pimpinan_rapat)->first();
        $notulis = Pegawai::where('nama', $data->notulen->notulis)->first();

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadView('backend.agenda-rapat.pdf.notulen', compact('data', 'pimpinan', 'notulis'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Notulen_Rapat_' . Str::slug($data->nama) . '.pdf');
    }

    // =====================================================
    // EXPORT DAFTAR HADIR (PDF)
    // =====================================================
    public function exportDaftarHadir($id)
    {
        $data = AgendaRapat::with('peserta', 'pegawai')->findOrFail($id);

        $pdf = Pdf::setOptions(['isRemoteEnabled' => true])->loadView('backend.agenda-rapat.pdf.daftar-hadir', compact('data'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Daftar_Hadir_' . Str::slug($data->nama) . '.pdf');
    }

    // =====================================================
    // PESERTA: EDIT
    // =====================================================
    public function editPeserta($pesertaId)
    {
        $peserta = \App\Models\RapatPeserta::findOrFail($pesertaId);
        $page = (object)[
            'title' => 'Peserta Rapat',
            'code'  => 'agenda-rapat.peserta',
            'url'   => 'agenda-rapat/peserta',
        ];
        return view('backend.agenda-rapat.edit-peserta', compact('peserta', 'page'));
    }

    // =====================================================
    // PESERTA: UPDATE
    // =====================================================
    public function updatePeserta(Request $request, $pesertaId)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'nip'      => 'nullable|string|max:50',
            'jabatan'  => 'nullable|string|max:255',
            'instansi' => 'nullable|string|max:255',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        $peserta = \App\Models\RapatPeserta::findOrFail($pesertaId);
        $peserta->update([
            'nama'     => $request->nama,
            'nip'      => $request->nip,
            'jabatan'  => $request->jabatan,
            'instansi' => $request->instansi,
            'no_hp'    => $request->no_hp,
        ]);

        return response()->json(['status' => true, 'message' => 'Data peserta berhasil diperbarui']);
    }

    // =====================================================
    // PESERTA: DELETE (confirm view)
    // =====================================================
    public function deletePeserta($pesertaId)
    {
        $peserta = \App\Models\RapatPeserta::findOrFail($pesertaId);
        $page = (object)[
            'title' => 'Peserta Rapat',
            'code'  => 'agenda-rapat.peserta',
            'url'   => 'agenda-rapat/peserta',
        ];
        return view('backend.agenda-rapat.delete-peserta', compact('peserta', 'page'));
    }

    // =====================================================
    // PESERTA: DESTROY
    // =====================================================
    public function destroyPeserta($pesertaId)
    {
        $peserta = \App\Models\RapatPeserta::findOrFail($pesertaId);
        if ($peserta->delete()) {
            return response()->json(['status' => true, 'message' => 'Peserta berhasil dihapus']);
        }
        return response()->json(['status' => false, 'message' => 'Peserta gagal dihapus']);
    }

    // =====================================================
    // TANDA TANGAN ELEKTRONIK (BSrE)
    // =====================================================
    public function signDokumen(Request $request, $id, $jenis)
    {
        // Validasi jenis dokumen
        if (!in_array($jenis, ['undangan', 'daftar_hadir', 'notulen', 'notulen_notulis', 'notulen_pimpinan'])) {
            return response()->json(['status' => false, 'message' => 'Jenis dokumen tidak valid.'], 422);
        }

        // Validasi passphrase
        $request->validate([
            'passphrase' => 'required|string',
        ]);

        // Cari agenda rapat
        $agenda = AgendaRapat::with('pegawai', 'notulen', 'peserta', 'user')->findOrFail($id);

        // Pastikan status DITERIMA
        if ($agenda->status !== 'DITERIMA') {
            return response()->json(['status' => false, 'message' => 'Dokumen hanya bisa ditandatangani jika status agenda DITERIMA.'], 403);
        }

        // Tentukan pegawai yang berhak tanda tangan berdasarkan jenis
        if ($jenis === 'notulen_notulis') {
            $pegawai = Pegawai::find($agenda->notulen->notulis_id ?? null);
        } elseif ($jenis === 'notulen_pimpinan') {
            $pegawai = Pegawai::find($agenda->notulen->pimpinan_rapat_id ?? null);
        } else {
            $pegawai = $agenda->pegawai;
        }

        if (!$pegawai || $pegawai->user_id !== auth()->id()) {
            return response()->json([
                'status'  => false,
                'message' => 'Anda tidak memiliki hak untuk menandatangani dokumen ini.',
            ], 403);
        }

        // Cek NIK pegawai
        if (empty($pegawai->nik)) {
            return response()->json([
                'status'  => false,
                'message' => 'NIK pegawai belum diisi. Silakan lengkapi data NIK pada modul Pegawai.',
            ], 422);
        }

        // Cek apakah sudah pernah ditandatangani
        $existing = DokumenTte::where('agenda_rapat_id', $id)
            ->where('jenis_dokumen', $jenis)
            ->where('status', 'signed')
            ->first();

        if ($existing) {
            return response()->json([
                'status'  => false,
                'message' => 'Dokumen sudah ditandatangani sebelumnya pada ' . $existing->signed_at->format('d/m/Y H:i') . '.',
            ]);
        }

        // Validasi khusus per jenis
        if (in_array($jenis, ['notulen', 'notulen_notulis', 'notulen_pimpinan']) && !$agenda->notulen) {
            return response()->json(['status' => false, 'message' => 'Notulen rapat belum dibuat.'], 422);
        }
        
        if (in_array($jenis, ['notulen_notulis', 'notulen_pimpinan'])) {
            if ($agenda->notulen->status !== 'DISETUJUI') {
                return response()->json(['status' => false, 'message' => 'Notulen harus berstatus DISETUJUI sebelum ditandatangani.'], 422);
            }
        }
        
        if ($jenis === 'daftar_hadir' && $agenda->peserta->count() === 0) {
            return response()->json(['status' => false, 'message' => 'Belum ada peserta yang hadir.'], 422);
        }

        // Generate PDF sementara atau gunakan PDF yang sudah ditandatangani oleh pihak lain (untuk Notulen)
        $pdfPath = null;
        $tmpDir = storage_path('app/temp-tte');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        if ($jenis === 'notulen_pimpinan') {
            $existing = DokumenTte::where('agenda_rapat_id', $id)->where('jenis_dokumen', 'notulen_notulis')->where('status', 'signed')->first();
            if ($existing) {
                $pdfPath = $tmpDir . "/temp_append_" . time() . '.pdf';
                $existingFile = $existing->file()->where('alias', 'dokumen_tte_signed')->first();
                if ($existingFile && $existingFile->exists) {
                    $existingPath = \Illuminate\Support\Facades\Storage::disk($existingFile->disk)->path($existingFile->target);
                    copy($existingPath, $pdfPath);
                } else {
                    return response()->json(['status' => false, 'message' => 'Dokumen asli tidak ditemukan untuk append TTE'], 404);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Notulis harus menandatangani Notulen terlebih dahulu.'], 422);
            }
        }

        // Jika belum ada yang tanda tangan atau dokumen bukan notulen
        if (!$pdfPath) {
            $pdfPath = $this->generateTempPdf($agenda, $jenis);
        }

        if (!$pdfPath || !file_exists($pdfPath)) {
            return response()->json(['status' => false, 'message' => 'Gagal membuat dokumen PDF.'], 500);
        }

        // Kirim ke BSrE untuk ditandatangani
        $bsre = new BsreSignService();
        $result = $bsre->signPdf($pdfPath, $pegawai->nik, $request->passphrase);

        // Bersihkan PDF sementara
        @unlink($pdfPath);

        if ($result['success']) {
            // Simpan signed PDF
            $signedFilename = "signed_{$jenis}_{$id}_" . time() . ".pdf";
            $targetPath = 'dokumen-tte/' . $signedFilename;
            \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->put($targetPath, $result['signed_pdf']);

            // Catat di database
            $dokumen = DokumenTte::updateOrCreate(
                ['agenda_rapat_id' => $id, 'jenis_dokumen' => $jenis],
                [
                    'pegawai_id'  => $pegawai->id,
                    'signed_file' => $signedFilename,
                    'status'      => 'signed',
                    'bsre_response' => json_encode(['message' => $result['message']]),
                    'signed_at'   => now(),
                ]
            );

            // CREATE FILE RECORD
            $dokumen->file()->where('alias', 'dokumen_tte_signed')->delete();
            $dokumen->file()->create([
                'alias' => 'dokumen_tte_signed',
                'data' => [
                    'name' => $signedFilename,
                    'disk' => config('filesystems.default'),
                    'target' => $targetPath,
                ]
            ]);

            // Update status Notulen jika pimpinan sudah TTE (artinya selesai)
            if ($jenis === 'notulen_pimpinan') {
                $agenda->notulen->update(['status' => 'SELESAI']);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Dokumen berhasil ditandatangani secara elektronik.',
            ]);
        }

        // Gagal — catat di log
        DokumenTte::updateOrCreate(
            ['agenda_rapat_id' => $id, 'jenis_dokumen' => $jenis],
            [
                'pegawai_id' => $pegawai->id,
                'status'     => 'failed',
                'bsre_response' => json_encode(['message' => $result['message']]),
            ]
        );

        return response()->json([
            'status'  => false,
            'message' => $result['message'],
        ]);
    }

    /**
     * Download dokumen yang sudah ditandatangani
     */
    public function downloadSigned($id, $jenis)
    {
        $dokumen = DokumenTte::where('agenda_rapat_id', $id)
            ->where('jenis_dokumen', $jenis)
            ->where('status', 'signed')
            ->firstOrFail();

        $file = $dokumen->file()->where('alias', 'dokumen_tte_signed')->first();

        if (!$file || !$file->exists) {
            return response()->json(['status' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        return redirect()->to($file->link_download);
    }

    /**
     * Generate temporary PDF file for signing
     */
    private function generateTempPdf(AgendaRapat $agenda, string $jenis): ?string
    {
        $tmpDir = storage_path('app/temp-tte');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $tmpPath = $tmpDir . "/temp_{$jenis}_{$agenda->id}_" . time() . '.pdf';

        try {
            switch ($jenis) {
                case 'undangan':
                    $data = $agenda;
                    $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                        ->loadView('backend.agenda-rapat.pdf.undangan', compact('data'));
                    break;

                case 'daftar_hadir':
                    $data = $agenda;
                    $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                        ->loadView('backend.agenda-rapat.pdf.daftar-hadir', compact('data'));
                    break;

                case 'notulen':
                case 'notulen_notulis':
                case 'notulen_pimpinan':
                    $data = $agenda;
                    $pimpinan = Pegawai::find($agenda->notulen->pimpinan_rapat_id ?? null);
                    $notulis = Pegawai::find($agenda->notulen->notulis_id ?? null);
                    $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                        ->loadView('backend.agenda-rapat.pdf.notulen', compact('data', 'pimpinan', 'notulis'));
                    break;

                default:
                    return null;
            }

            $pdf->setPaper('A4', 'portrait');
            file_put_contents($tmpPath, $pdf->output());

            return $tmpPath;
        } catch (\Exception $e) {
            \Log::error('Generate PDF for TTE failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
