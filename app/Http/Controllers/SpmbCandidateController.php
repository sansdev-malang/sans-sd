<?php

namespace App\Http\Controllers;

use App\Models\SpmbCandidate;
use App\Services\SpmbIntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpmbCandidateController extends Controller
{
    protected SpmbIntegrationService $service;

    public function __construct(SpmbIntegrationService $service)
    {
        $this->service = $service;
    }

    /**
     * Tampilkan Daftar Calon Siswa Baru SPMB
     */
    public function index(Request $request): View
    {
        $query = SpmbCandidate::query();

        // 1. Ambil daftar tahun ajaran yang tersedia untuk filter dropdown
        $availableAcademicYears = SpmbCandidate::whereNotNull('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year')
            ->toArray();

        // Default tahun ajaran aktif jika ada
        $selectedYear = $request->query('academic_year');
        if ($selectedYear) {
            $query->where('academic_year', $selectedYear);
        }

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('mother_name', 'like', "%{$search}%");
            });
        }

        // 3. Filter Status SPMB
        if ($request->filled('status')) {
            $query->where('spmb_status', $request->query('status'));
        }

        // 4. Filter Status Pembayaran
        if ($request->filled('payment_status')) {
            $query->where('spmb_payment_status', $request->query('payment_status'));
        }

        // 5. Filter Status Siswa Aktif
        if ($request->filled('is_active_student')) {
            $query->where('is_active_student', $request->boolean('is_active_student'));
        }

        // 6. Filter Gelombang
        if ($request->filled('wave')) {
            $query->where('wave', $request->query('wave'));
        }

        // Stats Summary
        $statsBase = SpmbCandidate::query();
        if ($selectedYear) {
            $statsBase->where('academic_year', $selectedYear);
        }

        $stats = [
            'total' => (clone $statsBase)->count(),
            'verified' => (clone $statsBase)->whereIn('spmb_status', ['verified', 'completed', 'accepted', 'agreement_signed'])->count(),
            'paid' => (clone $statsBase)->where('spmb_payment_status', 'paid')->count(),
            'active_students' => (clone $statsBase)->where('is_active_student', true)->count(),
        ];

        $candidates = $query->latest('spmb_verified_at')->latest('id')->paginate(15)->withQueryString();

        return view('admin.spmb_candidates', compact('candidates', 'stats', 'availableAcademicYears', 'selectedYear'));
    }

    /**
     * Detail Modal / JSON Calon Siswa
     */
    public function show($id): JsonResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $candidate,
            'whatsapp_url' => $candidate->whatsapp_url,
        ]);
    }

    /**
     * Manual Trigger Sync dari SPMB (Pull)
     */
    public function sync(Request $request): RedirectResponse
    {
        $result = $this->service->syncCandidates();

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Toggle Status Menjadi Siswa Aktif Unit
     */
    public function toggleActive($id, Request $request): RedirectResponse
    {
        $candidate = SpmbCandidate::findOrFail($id);
        $newStatus = !$candidate->is_active_student;

        $candidate->update([
            'is_active_student' => $newStatus,
            'activated_at' => $newStatus ? now() : null,
            'assigned_class' => $request->input('assigned_class', $candidate->assigned_class),
        ]);

        $statusLabel = $newStatus ? 'dijadikan Siswa Aktif SD' : 'dibatalkan dari Siswa Aktif';
        return redirect()->back()->with('success', "Calon siswa [{$candidate->full_name}] berhasil {$statusLabel}.");
    }
}
