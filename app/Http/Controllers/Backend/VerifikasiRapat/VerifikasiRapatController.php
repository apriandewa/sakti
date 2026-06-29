<?php

namespace App\Http\Controllers\Backend\VerifikasiRapat;

use App\Http\Controllers\Controller;
use App\Models\AgendaRapat;
use App\Models\RapatVerifikasi;
use App\Models\DokumenTte;
use App\Models\Pegawai;
use App\Services\BsreSignService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Facades\Verification;
use Barryvdh\DomPDF\Facade\Pdf;

class VerifikasiRapatController extends Controller
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

        // Verifikator melihat yang status PENGAJUAN
        $query = AgendaRapat::with('user')->where('status', 'PENGAJUAN')->latest();

        $data = $query->get();

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
            ->addColumn('pembuat', function ($data) {
                return $data->user->name ?? '-';
            })
            ->addColumn('action', function ($data) use ($user) {
                $button = '';

                if ($user->read) {
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Detail & Verifikasi" data-action="show" data-url="' . $this->url . '" data-id="' . $data->id . '" data-size="modal-xl" title="Detail"><i class="fa fa-eye text-info"></i></button>';
                }

                return "<div class='btn-group'>" . $button . "</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'action'])
            ->make();
    }

    // =====================================================
    // SHOW (Detail + Form Verifikasi)
    // =====================================================
    public function show($id)
    {
        $data = AgendaRapat::with('user', 'verifikasi.user', 'pegawai')->findOrFail($id);

        // Histori verifikasi
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

        $dasar_surat = $data->getfilesbyalias('dasar_surat');
        $user = auth()->user();

        return view($this->view . '.show', compact('data', 'histori_verifikasi', 'dasar_surat', 'user'));
    }

    // =====================================================
    // UPDATE (Proses Verifikasi: Terima/Revisi/Tolak)
    // + Tanda Tangan Elektronik BSrE saat DITERIMA
    // =====================================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'status'     => 'required|in:DITERIMA,REVISI,DITOLAK',
            'catatan'    => 'required|string',
            'passphrase' => 'nullable|string',
        ]);

        $user = $request->user();
        $agendaRapat = AgendaRapat::with('pegawai', 'user')->findOrFail($id);

        // ============================================================
        // Jika DITERIMA → proses TTE BSrE jika jenis TTE elektronik
        // ============================================================
        $tteResult = null;
        if ($request->status === 'DITERIMA') {
            if ($agendaRapat->jenis_tanda_tangan === 'elektronik') {
                $pegawai = $agendaRapat->pegawai;
                if (!$pegawai) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Pegawai penanda tangan tidak ditemukan.',
                    ], 422);
                }
                if ($pegawai->user_id !== $user->id) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Anda tidak memiliki hak untuk menandatangani dokumen ini. Penanda tangan yang sah adalah ' . $pegawai->nama,
                    ], 422);
                }
                if (empty($pegawai->nik)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'NIK Pegawai penanda tangan belum dikonfigurasi.',
                    ], 422);
                }
                if (empty($request->passphrase)) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Passphrase wajib diisi untuk tanda tangan elektronik.',
                    ], 422);
                }

                // Cek belum pernah ditandatangani
                $alreadySigned = DokumenTte::where('agenda_rapat_id', $id)
                    ->where('jenis_dokumen', 'undangan')
                    ->where('status', 'signed')
                    ->exists();

                if (!$alreadySigned) {
                    // Generate PDF undangan sementara
                    $pdfPath = $this->generateUndanganPdf($agendaRapat);

                    if (!$pdfPath || !file_exists($pdfPath)) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Gagal membuat file PDF undangan sementara.',
                        ], 422);
                    }

                    // Kirim ke BSrE
                    $bsre = new BsreSignService();
                    $result = $bsre->signPdf($pdfPath, $pegawai->nik, $request->passphrase);

                    // Bersihkan file sementara
                    @unlink($pdfPath);

                    if ($result['success']) {
                        // Simpan signed PDF
                        $signedFilename = "signed_undangan_{$id}_" . time() . ".pdf";
                        $targetPath = 'dokumen-tte/' . $signedFilename;
                        \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->put($targetPath, $result['signed_pdf']);

                        // Catat di database
                        $dokumen = DokumenTte::updateOrCreate(
                            ['agenda_rapat_id' => $id, 'jenis_dokumen' => 'undangan'],
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

                        $tteResult = 'success';
                    } else {
                        // TTE gagal — return error, jangan ubah status verifikasi ke DITERIMA
                        DokumenTte::updateOrCreate(
                            ['agenda_rapat_id' => $id, 'jenis_dokumen' => 'undangan'],
                            [
                                'pegawai_id' => $pegawai->id,
                                'status'     => 'failed',
                                'bsre_response' => json_encode(['message' => $result['message']]),
                            ]
                        );

                        return response()->json([
                            'status'  => false,
                            'message' => 'Proses tanda tangan BSrE gagal: ' . $result['message'],
                        ], 422);
                    }
                }
            }
        }

        // Update status agenda rapat
        $agendaRapat->update([
            'status'  => $request->status,
        ]);

        // Simpan histori verifikasi
        RapatVerifikasi::create([
            'agenda_rapat_id' => $id,
            'user_id'         => $user->id,
            'status'          => $request->status,
            'catatan'         => $request->catatan,
        ]);

        // Pesan respons
        $messages = [
            'DITERIMA' => 'Agenda rapat telah DITERIMA.',
            'REVISI'   => 'Agenda rapat dikembalikan untuk REVISI.',
            'DITOLAK'  => 'Agenda rapat telah DITOLAK.',
        ];

        $message = $messages[$request->status] ?? 'Verifikasi berhasil disimpan';

        // Tambahkan info TTE ke pesan
        if ($request->status === 'DITERIMA') {
            if ($tteResult === 'success') {
                $message .= ' Surat undangan berhasil ditandatangani secara elektronik (BSrE).';
            } elseif ($tteResult) {
                $message .= ' Namun TTE gagal: ' . $tteResult . '. Surat undangan tanpa tanda tangan elektronik.';
            } else {
                $message .= ' Undangan rapat bisa didownload.';
            }
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
        ]);
    }

    /**
     * Generate temporary undangan PDF for BSrE signing
     */
    private function generateUndanganPdf(AgendaRapat $agenda): ?string
    {
        $tmpDir = storage_path('app/temp-tte');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $tmpPath = $tmpDir . "/temp_undangan_{$agenda->id}_" . time() . '.pdf';

        try {
            $data = $agenda;
            $pdf = Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('backend.agenda-rapat.pdf.undangan', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            file_put_contents($tmpPath, $pdf->output());

            return $tmpPath;
        } catch (\Exception $e) {
            \Log::error('Generate Undangan PDF for TTE failed', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
