<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SpmbCandidate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpmbIntegrationService
{
    /**
     * Ambil Base URL SPMB dari database settings
     */
    public function getApiUrl(): string
    {
        return rtrim(Setting::get('spmb_api_url', config('services.spmb.url', 'http://sans-spmb.test')), '/');
    }

    /**
     * Ambil Bearer Token API SPMB
     */
    public function getApiToken(): ?string
    {
        return Setting::get('spmb_api_token', config('services.spmb.token'));
    }

    /**
     * Ambil Webhook Secret Key SPMB
     */
    public function getWebhookSecret(): ?string
    {
        return Setting::get('spmb_webhook_secret', config('services.spmb.webhook_secret'));
    }

    /**
     * Test Koneksi API ke SPMB Pusat
     */
    public function testConnection(): array
    {
        $url = $this->getApiUrl();
        $token = $this->getApiToken();

        if (empty($url) || empty($token)) {
            return [
                'success' => false,
                'message' => 'URL Aplikasi SPMB atau Token Kunci API belum diisi di Pengaturan.',
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
                $total = $data['meta']['total'] ?? 0;
                $clientName = $data['client']['name'] ?? 'Client Unit';

                return [
                    'success' => true,
                    'message' => "Koneksi berhasil! Terhubung sebagai [{$clientName}] dengan {$total} calon murid terdeteksi.",
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke SPMB: ' . ($response->json('message') ?? 'HTTP Status ' . $response->status()),
                'status_code' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Kesalahan koneksi jaringan: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Tarik Data Calon Murid dari SPMB (Pull Sync)
     */
    public function syncCandidates(array $filters = []): array
    {
        $url = $this->getApiUrl();
        $token = $this->getApiToken();

        if (empty($url) || empty($token)) {
            return [
                'success' => false,
                'message' => 'Konfigurasi URL atau Token API SPMB belum lengkap.',
                'synced_count' => 0,
            ];
        }

        $page = 1;
        $totalSynced = 0;
        $syncedIds = [];
        $hasMore = true;

        try {
            while ($hasMore) {
                $queryParams = array_merge($filters, [
                    'page' => $page,
                    'per_page' => 50,
                ]);

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
                    $savedCandidate = $this->upsertCandidateFromPayload($cand);
                    $regId = $savedCandidate->spmb_registration_id ?? ($cand['id'] ?? null);
                    if ($regId) {
                        $syncedIds[] = (int) $regId;
                    }
                    $totalSynced++;
                }

                if ($page >= ($meta['last_page'] ?? 1) || empty($candidates)) {
                    $hasMore = false;
                } else {
                    $page++;
                }
            }

            // Full Mirroring (Prune data yang tidak lagi diizinkan / tidak ada di SPMB)
            $syncedIds = array_values(array_filter(array_unique($syncedIds)));
            $pruneQuery = SpmbCandidate::query();
            if (!empty($filters['period']) && $filters['period'] !== 'all') {
                $pruneQuery->where('academic_year', $filters['period']);
            }
            if (!empty($syncedIds)) {
                $pruneQuery->whereNotIn('spmb_registration_id', $syncedIds);
            }
            $prunedCount = $pruneQuery->delete();

            $msg = "Berhasil menyinkronkan {$totalSynced} data calon murid dari SPMB.";
            if ($prunedCount > 0) {
                $msg .= " ({$prunedCount} data lama yang tidak lagi masuk izin SPMB telah dibersihkan).";
            }

            return [
                'success' => true,
                'message' => $msg,
                'synced_count' => $totalSynced,
                'pruned_count' => $prunedCount,
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
     * Upsert Kandidat ke Database Unit
     */
    public function upsertCandidateFromPayload(array $payload): SpmbCandidate
    {
        $registrationId = $payload['id'] ?? ($payload['registration_id'] ?? ($payload['spmb_registration_id'] ?? null));
        if (!$registrationId) {
            throw new \InvalidArgumentException('Payload harus memiliki id / registration_id.');
        }

        $bio = $payload['student_bio'] ?? ($payload['candidate_bio'] ?? []);
        $address = $bio['address'] ?? ($payload['address'] ?? []);
        $parents = $payload['parent_info'] ?? ($payload['parents'] ?? []);
        $father = $parents['father'] ?? [];
        $mother = $parents['mother'] ?? [];
        $guardian = $parents['guardian'] ?? ($payload['guardian'] ?? []);
        $contact = $parents['primary_contact'] ?? ($payload['primary_contact'] ?? []);
        $schoolOrigin = $payload['school_origin'] ?? [];
        $unit = $payload['unit'] ?? [];

        // Gender formatting
        $gender = $bio['gender'] ?? ($payload['gender'] ?? null);
        if ($gender) {
            $genderLower = strtolower($gender);
            if (str_contains($genderLower, 'laki') || $genderLower === 'male' || $genderLower === 'l') {
                $gender = 'male';
            } elseif (str_contains($genderLower, 'perempuan') || $genderLower === 'female' || $genderLower === 'p') {
                $gender = 'female';
            }
        }

        // Birth Date
        $birthDate = null;
        if (!empty($bio['birth_date'])) {
            try {
                $birthDate = \Carbon\Carbon::parse($bio['birth_date'])->format('Y-m-d');
            } catch (\Exception $e) {
                $birthDate = null;
            }
        }

        $verifiedAt = null;
        if (!empty($payload['verified_at'])) {
            try {
                $verifiedAt = \Carbon\Carbon::parse($payload['verified_at']);
            } catch (\Exception $e) {
                $verifiedAt = null;
            }
        }

        $registeredAt = null;
        if (!empty($payload['created_at'])) {
            try {
                $registeredAt = \Carbon\Carbon::parse($payload['created_at']);
            } catch (\Exception $e) {
                $registeredAt = null;
            }
        }

        $parentPhone = $contact['whatsapp'] ?? ($parents['primary_whatsapp'] ?? ($father['phone'] ?? ($mother['phone'] ?? ($guardian['phone'] ?? ($payload['parent_phone'] ?? null)))));

        $fullAddress = is_string($address) ? $address : ($address['full_address'] ?? ($address['street'] ?? ($address['street_address'] ?? null)));

        return SpmbCandidate::updateOrCreate(
            ['spmb_registration_id' => $registrationId],
            [
                'registration_number' => $payload['registration_number'] ?? ($payload['registration_no'] ?? null),
                'full_name' => $bio['full_name'] ?? ($payload['candidate_name'] ?? ($payload['full_name'] ?? 'Calon Siswa')),
                'nickname' => $bio['nickname'] ?? null,
                'gender' => $gender,
                'birth_place' => $bio['birth_place'] ?? null,
                'birth_date' => $birthDate,
                'nik' => $bio['nik'] ?? null,
                'nisn' => $bio['nisn'] ?? null,
                'child_number' => $bio['child_number'] ?? null,
                'siblings_count' => $bio['siblings_count'] ?? null,

                // Academic
                'target_unit' => $unit['code'] ?? ($unit['name'] ?? ($payload['target_unit'] ?? 'SD')),
                'target_class' => $payload['class_program'] ?? ($payload['target_class'] ?? 'Kelas 1'),
                'academic_year' => $payload['period'] ?? ($payload['academic_year'] ?? null),
                'wave' => $payload['wave'] ?? null,

                // Contact & Parents
                'parent_phone' => $parentPhone,
                'father_name' => $father['name'] ?? null,
                'father_job' => $father['job'] ?? ($father['occupation'] ?? null),
                'father_phone' => $father['phone'] ?? null,
                'mother_name' => $mother['name'] ?? null,
                'mother_job' => $mother['job'] ?? ($mother['occupation'] ?? null),
                'mother_phone' => $mother['phone'] ?? null,
                'guardian_name' => $guardian['name'] ?? null,
                'guardian_phone' => $guardian['phone'] ?? null,

                // Address
                'address' => $fullAddress,
                'rt' => is_array($address) ? ($address['rt'] ?? null) : null,
                'rw' => is_array($address) ? ($address['rw'] ?? null) : null,
                'village' => is_array($address) ? ($address['village'] ?? null) : null,
                'district' => is_array($address) ? ($address['district'] ?? null) : null,
                'city' => is_array($address) ? ($address['city'] ?? null) : null,
                'province' => is_array($address) ? ($address['province'] ?? null) : null,
                'postal_code' => is_array($address) ? ($address['postal_code'] ?? null) : null,

                // School Origin
                'previous_school' => $schoolOrigin['previous_school'] ?? null,
                'previous_school_npsn' => $schoolOrigin['npsn'] ?? null,
                'previous_school_address' => $schoolOrigin['school_address'] ?? null,

                // Status
                'spmb_status' => $payload['registration_status'] ?? 'verified',
                'spmb_payment_status' => $payload['payment_status'] ?? 'unpaid',
                'spmb_verified_at' => $verifiedAt,
                'spmb_registered_at' => $registeredAt,

                // Snapshots
                'documents' => $payload['documents'] ?? null,
                'payments_data' => $payload['payments'] ?? null,
                'raw_payload' => $payload,
            ]
        );
    }

    /**
     * Validasi HMAC SHA256 Webhook Signature
     */
    public function verifyWebhookSignature(string $payloadContent, ?string $signatureHeader): bool
    {
        $secret = $this->getWebhookSecret();
        if (empty($secret) || empty($signatureHeader)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payloadContent, $secret);
        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Proses Webhook Event dari SPMB
     */
    public function processWebhookEvent(array $eventPayload): array
    {
        $event = $eventPayload['event'] ?? 'unknown';
        $data = $eventPayload['data'] ?? [];

        Log::info("[SPMB Webhook Received] Event: {$event}", ['data_id' => $data['id'] ?? ($data['registration_id'] ?? null)]);

        switch ($event) {
            case 'ping':
                return [
                    'success' => true,
                    'message' => 'Pong! Webhook endpoint aktif dan terhubung dengan aman.',
                ];

            case 'candidate.verified':
            case 'payment.success':
            case 'candidate.updated':
                if (!empty($data['id']) || !empty($data['registration_id'])) {
                    $candidate = $this->upsertCandidateFromPayload($data);
                    return [
                        'success' => true,
                        'message' => "Data calon murid [{$candidate->full_name}] berhasil diperbarui secara otomatis.",
                        'candidate_id' => $candidate->id,
                    ];
                }
                return [
                    'success' => false,
                    'message' => 'Data payload event tidak lengkap.',
                ];

            default:
                return [
                    'success' => true,
                    'message' => "Event '{$event}' diterima namun diabaikan.",
                ];
        }
    }
}
