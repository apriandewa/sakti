<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BsreSignService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl  = (string) config('services.bsre.url', '');
        $this->username = (string) config('services.bsre.username', '');
        $this->password = (string) config('services.bsre.password', '');
    }

    /**
     * Tanda tangani PDF menggunakan BSrE
     *
     * @param string $pdfPath    Path absolut ke file PDF yang akan ditandatangani
     * @param string $nik        NIK penanda tangan (terdaftar di BSrE)
     * @param string $passphrase Passphrase dari penanda tangan (TIDAK BOLEH DISIMPAN)
     * @return array ['success' => bool, 'signed_pdf' => binary|null, 'message' => string]
     */
    public function signPdf(string $pdfPath, string $nik, string $passphrase): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(60)
                ->attach('file', file_get_contents($pdfPath), basename($pdfPath))
                ->post($this->baseUrl . '/sign/pdf', [
                    'nik'        => $nik,
                    'passphrase' => $passphrase,
                    'tampilan'   => 'invisible',
                ]);

            if ($response->successful()) {
                return [
                    'success'    => true,
                    'signed_pdf' => $response->body(),
                    'message'    => 'Dokumen berhasil ditandatangani secara elektronik.',
                ];
            }

            $errorMsg = 'Gagal menandatangani dokumen.';
            if ($response->status() === 401) {
                $errorMsg = 'Passphrase salah atau sertifikat tidak valid.';
            } elseif ($response->status() === 404) {
                $errorMsg = 'NIK tidak terdaftar di BSrE.';
            } else {
                $errorMsg = $response->json('error') ?? $response->json('message') ?? $errorMsg;
            }

            Log::warning('BSrE Sign Failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'nik'    => substr($nik, 0, 6) . '****',
            ]);

            return [
                'success'    => false,
                'signed_pdf' => null,
                'message'    => $errorMsg,
            ];
        } catch (\Exception $e) {
            Log::error('BSrE Sign Exception', ['error' => $e->getMessage()]);

            return [
                'success'    => false,
                'signed_pdf' => null,
                'message'    => 'Koneksi ke server BSrE gagal. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Verifikasi dokumen PDF yang sudah ditandatangani
     */
    public function verifyPdf(string $pdfPath): array
    {
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->attach('signed_file', file_get_contents($pdfPath), basename($pdfPath))
                ->post($this->baseUrl . '/sign/verify');

            return [
                'success' => $response->successful(),
                'data'    => $response->json(),
                'message' => $response->successful() ? 'Dokumen valid.' : 'Verifikasi gagal.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data'    => null,
                'message' => 'Gagal memverifikasi dokumen: ' . $e->getMessage(),
            ];
        }
    }
}
