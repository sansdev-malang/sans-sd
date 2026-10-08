<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SpmbCandidate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpmbIntegrationService
{
    /**
     * Ambil Base URL SPMB dari database settings / config
     */
    public static function getBaseUrl(): string
    {
        $url = Setting::get('spmb_api_url', config('services.spmb.url', 'http://sans-spmb.test'));
        return rtrim($url, '/');
    }

    public function getApiUrl(): string
    {
        return self::getBaseUrl();
    }

    /**
     * Ambil Bearer Token API SPMB
     */
    public static function getApiToken(): ?string
    {
        return Setting::get('spmb_api_token', config('services.spmb.token'));
    }

    /**
     * Ambil Webhook Secret Key SPMB
     */
    public static function getWebhookSecret(): ?string
    {
        return Setting::get('spmb_webhook_secret', config('services.spmb.webhook_secret'));
    }

    /**
     * Test Koneksi API ke SPMB Pusat
     */
    public function testConnection(): array
    {
        $url = self::getBaseUrl();
        $token = self::getApiToken();

        if (empty($url) || empty($token)) {
            return [
                'success' => false,
                'message' => 'URL Aplikasi SPMB atau Token Kunci API belum diisi di Pengaturan Sistem.',
            ];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get("{$url}/api/v1/candidates", [
                    'per_page' => 1,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $total = $data['meta']['total'] ?? count($data['data'] ?? []);
                $clientName = $data['client']['name'] ?? 'Client Unit SD';

                return [
                    'success' => true,
                    'status_code' => $response->status(),
                    'message' => "Koneksi berhasil! Terhubung sebagai [{$clientName}] dengan {$total} calon murid terdeteksi di SPMB Pusat.",
                    'data' => $data,
                ];
            }

            if ($response->status() === 401) {
                return [
                    'success' => false,
                    'status_code' => 401,
                    'message' => 'Autentikasi Gagal: Token API SPMB tidak valid atau telah dicabut.',
                ];
            }

            if ($response->status() === 403) {
                return [
                    'success' => false,
                    'status_code' => 403,
                    'message' => 'Akses Ditolak: Client API tidak memiliki hak akses untuk Unit SD.',
                ];
            }

            return [
                'success' => false,
                'status_code' => $response->status(),
                'message' => 'Gagal terhubung ke SPMB: ' . ($response->json('message') ?? 'HTTP Status ' . $response->status()),
            ];
        } catch (\Throwable $e) {
            Log::error('SPMB test connection error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Kesalahan koneksi jaringan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tarik Data Calon Murid dari SPMB (Pull Sync)
     */
    public function syncCandidates($period = null, $status = null): array
    {
        $url = self::getBaseUrl();
        $token = self::getApiToken();

        if (empty($url) || empty($token)) {
            return [
                'success' => false,
                'message' => 'Konfigurasi URL atau Token API SPMB belum lengkap di Pengaturan.',
                'synced_count' => 0,
            ];
        }

        // Support passing array of filters or individual string args
        if (is_array($period)) {
            $filters = $period;
            $period = $filters['period'] ?? null;
            $status = $filters['status'] ?? null;
        }

        $page = 1;
        $syncedIds = [];
        $totalSynced = 0;
        $errors = [];

        try {
            do {
                $queryParams = [
                    'page' => $page,
                    'per_page' => 50,
                ];
                if ($period && $period !== 'all') {
                    $queryParams['period'] = $period;
                }
                if ($status && $status !== 'all') {
                    $queryParams['status'] = $status;
                }

                $response = Http::withToken($token)
                    ->acceptJson()
                    ->timeout(20)
                    ->get("{$url}/api/v1/candidates", $queryParams);

                if (!$response->successful()) {
                    Log::error('[SPMB Sync] Pull failed', ['response' => $response->body()]);
                    return [
                        'success' => false,
                        'message' => 'Gagal mengambil data dari SPMB: ' . ($response->json('message') ?? 'HTTP ' . $response->status()),
                        'synced_count' => $totalSynced,
                    ];
                }

                $json = $response->json();
                $candidates = $json['data'] ?? [];
                $meta = $json['meta'] ?? [];

                foreach ($candidates as $cand) {
                    try {
                        $savedCandidate = SpmbCandidate::syncFromPayload($cand);
                        $regId = $savedCandidate->spmb_registration_id ?? ($cand['id'] ?? null);
                        if ($regId) {
                            $syncedIds[] = (int) $regId;
                        }
                        $totalSynced++;
                    } catch (\Throwable $ex) {
                        $errors[] = "No. Reg " . ($cand['registration_number'] ?? '?') . ": " . $ex->getMessage();
                    }
                }

                $lastPage = $meta['last_page'] ?? 1;
                $page++;
            } while ($page <= $lastPage);

            // Full Mirroring (Prune data yang tidak lagi diizinkan / tidak ada di SPMB)
            $syncedIds = array_values(array_filter(array_unique($syncedIds)));
            $pruneQuery = SpmbCandidate::query();
            if (!empty($period) && $period !== 'all') {
                $slashPeriod = str_replace('-', '/', $period);
                $hyphenPeriod = str_replace('/', '-', $period);
                $pruneQuery->where(function($q) use ($slashPeriod, $hyphenPeriod) {
                    $q->where('academic_year', $slashPeriod)
                      ->orWhere('academic_year', $hyphenPeriod);
                });
            }
            if (!empty($syncedIds)) {
                $pruneQuery->whereNotIn('spmb_registration_id', $syncedIds);
            }
            $prunedCount = $pruneQuery->delete();

            $periodLabel = $period && $period !== 'all' ? " Tapel " . str_replace('-', '/', $period) : "";
            if ($totalSynced > 0) {
                $msg = "Berhasil menyinkronkan {$totalSynced} data calon murid dari SPMB Pusat{$periodLabel}.";
            } else {
                $msg = "Sinkronisasi selesai. Belum ada calon murid baru yang memenuhi kriteria administrasi/lolos di SPMB{$periodLabel}.";
            }
            if ($prunedCount > 0) {
                $msg .= " ({$prunedCount} data lama yang tidak lagi masuk izin SPMB telah dibersihkan).";
            }

            return [
                'success' => true,
                'message' => $msg,
                'synced_count' => $totalSynced,
                'pruned_count' => $prunedCount,
                'errors' => $errors,
            ];
        } catch (\Throwable $e) {
            Log::error('[SPMB Sync] Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage(),
                'synced_count' => $totalSynced,
            ];
        }
    }

    /**
     * Validasi HMAC SHA256 Webhook Signature
     */
    public function verifyWebhookSignature(string $payloadContent, ?string $signatureHeader): bool
    {
        $secret = self::getWebhookSecret();
        if (empty($secret)) {
            return false;
        }

        if (empty($signatureHeader)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payloadContent, $secret);
        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Proses Webhook Event dari SPMB
     */
    public function processWebhookEvent($event, $payload = []): array
    {
        if (is_array($event) && empty($payload)) {
            $eventPayload = $event;
            $event = $eventPayload['event'] ?? 'unknown';
            $payload = $eventPayload['data'] ?? $eventPayload;
        }

        Log::info("[SPMB Webhook Received] Event: {$event}", ['payload' => $payload]);

        if ($event === 'ping') {
            return [
                'success' => true,
                'status' => 'pong',
                'message' => 'Webhook ping received successfully by SANS SD.',
                'timestamp' => now()->toIso8601String(),
            ];
        }

        // Candidate events
        if (in_array($event, [
            'candidate.agreement_signed', 
            'candidate.verified', 
            'candidate.accepted', 
            'candidate.created', 
            'candidate.updated',
            'payment.tuition_paid', 
            'payment.success', 
            'registration.completed'
        ])) {
            $candidateData = $payload['data'] ?? $payload;
            if (!empty($candidateData)) {
                $candidate = SpmbCandidate::syncFromPayload($candidateData);
                return [
                    'success' => true,
                    'status' => 'success',
                    'event' => $event,
                    'registration_number' => $candidate->registration_number,
                    'full_name' => $candidate->full_name,
                    'message' => "Calon murid {$candidate->full_name} ({$candidate->registration_number}) berhasil diperbarui secara otomatis.",
                    'candidate_id' => $candidate->id,
                ];
            }
        }

        return [
            'success' => true,
            'status' => 'ignored',
            'message' => "Event {$event} tidak membutuhkan pemrosesan khusus.",
        ];
    }

    /**
     * Get dynamic master filter options from SPMB API with fallback for SD
     */
    public function getFilterOptions(): array
    {
        $baseUrl = self::getBaseUrl();
        $token = self::getApiToken();

        if (!empty($baseUrl) && !empty($token)) {
            try {
                $response = Http::withToken($token)
                    ->timeout(4)
                    ->acceptJson()
                    ->get("{$baseUrl}/api/v1/options");

                if ($response->successful()) {
                    $data = $response->json('data');
                    if (!empty($data) && is_array($data)) {
                        return $data;
                    }
                }
            } catch (\Throwable $e) {
                // Ignore and use master fallback
            }
        }

        // Fallback default master for SD
        return [
            'periods' => ['2027/2028', '2026/2027', '2028/2029', '2029/2030'],
            'default_period' => '2027/2028',
            'registration_types' => ['Murid Baru', 'Mutasi Masuk / Pindahan'],
            'waves' => ['Indent', 'Gelombang 1', 'Gelombang 2'],
            'categories' => ['Reguler', 'Murid Berkebutuhan Khusus (MBK)'],
            'grades' => [
                ['name' => 'Kelas 1', 'jenjang_code' => 'SD'],
                ['name' => 'Kelas 2', 'jenjang_code' => 'SD'],
                ['name' => 'Kelas 3', 'jenjang_code' => 'SD'],
                ['name' => 'Kelas 4', 'jenjang_code' => 'SD'],
                ['name' => 'Kelas 5', 'jenjang_code' => 'SD'],
                ['name' => 'Kelas 6', 'jenjang_code' => 'SD'],
            ],
        ];
    }
}
