<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SpmbIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SpmbWebhookController extends Controller
{
    protected SpmbIntegrationService $service;

    public function __construct(SpmbIntegrationService $service)
    {
        $this->service = $service;
    }

    /**
     * Endpoint Penerima Webhook SPMB (Push Real-time)
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $rawContent = $request->getContent();
        $signature = $request->header('X-Spmb-Signature');

        // Validasi Signature Keamanan HMAC
        if (!$this->service->verifyWebhookSignature($rawContent, $signature)) {
            Log::warning('[SPMB Webhook Rejected] Invalid signature or unauthorized call', [
                'ip' => $request->ip(),
                'signature' => $signature,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid webhook signature or secret key mismatch.',
            ], 401);
        }

        $payload = $request->json()->all();
        $result = $this->service->processWebhookEvent($payload);

        return response()->json([
            'status' => $result['success'] ? 'success' : 'failed',
            'message' => $result['message'],
        ], $result['success'] ? 200 : 422);
    }
}
