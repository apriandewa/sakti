<?php

namespace App\Services\Ekinerja;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Wrapper HTTP murni ke API BKN e-Kinerja. Tidak menyentuh database sama
 * sekali — hanya bertugas request/response + error handling. Query &
 * caching lokal ada di EkinerjaService, bukan di sini.
 */
class BknEkinerjaClient
{
    protected string $baseUrl;
    protected ?string $token;
    protected int $timeout;
    protected int $connectTimeout;
    protected int $retryTimes;
    protected int $retrySleep;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('ekinerja.base_url'), '/');
        $this->token = config('ekinerja.token');
        $this->timeout = (int) config('ekinerja.timeout', 15);
        $this->connectTimeout = (int) config('ekinerja.connect_timeout', 10);
        $this->retryTimes = (int) config('ekinerja.retry.times', 2);
        $this->retrySleep = (int) config('ekinerja.retry.sleep', 500);
    }

    /**
     * GET /api_kinerja/referensi/periode
     * @return array<int, array<string, mixed>>
     */
    public function getReferensiPeriode(): array
    {
        $url = $this->baseUrl . config('ekinerja.endpoints.referensi_periode');

        $response = $this->safeGet($url, [], 'referensi periode');

        return $response->json('data', []) ?? [];
    }

    /**
     * GET /api_kinerja/laporan/penilaian/{tahun}/{periode_id}?nip=...
     * @return array<string, mixed>|null null jika data tidak ditemukan (404 / data kosong)
     */
    public function getPenilaian(int $tahun, string $periodeId, string $nip): ?array
    {
        $path = str_replace(['{tahun}', '{periode_id}'], [$tahun, $periodeId], config('ekinerja.endpoints.laporan_penilaian'));
        $url = $this->baseUrl . $path;

        $response = $this->safeGet($url, ['nip' => $nip], "penilaian NIP {$nip}", allow404: true);

        if ($response === null) {
            return null;
        }

        $data = $response->json('data', []) ?? [];

        return $data[0] ?? null;
    }

    /** @return \Illuminate\Http\Client\Response|null */
    protected function safeGet(string $url, array $query, string $context, bool $allow404 = false)
    {
        try {
            $response = $this->client()->get($url, $query);
        } catch (Throwable $e) {
            Log::error("[Ekinerja] Gagal mengambil {$context}: " . $e->getMessage());
            throw new BknApiException("Layanan BKN ({$context}) sedang tidak dapat diakses.", 0, $e);
        }

        if ($allow404 && $response->status() === 404) {
            return null;
        }

        $this->guardResponse($response, $context);

        return $response;
    }

    protected function client(): PendingRequest
    {
        if (empty($this->token)) {
            throw new BknApiException('Token BKN belum dikonfigurasi. Set EKINERJA_BKN_TOKEN pada .env.');
        }

        return Http::withToken($this->token)
            ->acceptJson()
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->retry(
                $this->retryTimes,
                $this->retrySleep,
                fn (Throwable $e) => $e instanceof RequestException
                    && ! in_array($e->response->status(), [401, 403, 404], true)
            );
    }

    protected function guardResponse($response, string $context): void
    {
        if (in_array($response->status(), [401, 403], true)) {
            Log::warning("[Ekinerja] Token BKN ditolak saat mengambil {$context} (HTTP {$response->status()}). Token kemungkinan sudah kedaluwarsa.");
            throw new BknApiException('Token integrasi BKN tidak valid atau sudah kedaluwarsa.', 401);
        }

        if ($response->serverError()) {
            throw new BknApiException("Layanan BKN ({$context}) sedang mengalami gangguan.", $response->status());
        }

        if ($response->clientError()) {
            throw new BknApiException("Permintaan ke layanan BKN ({$context}) ditolak (HTTP {$response->status()}).", $response->status());
        }
    }
}
