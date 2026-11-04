<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $group = $request->get('group', 'all');

        $query = Subject::withCount('assignments');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($group && $group !== 'all') {
            $query->where('group', $group);
        }

        $subjects = $query->orderBy('order', 'asc')->orderBy('code', 'asc')->get();

        $stats = [
            'total' => Subject::count(),
            'active' => Subject::where('is_active', true)->count(),
            'keislaman' => Subject::where('group', 'Keislaman')->count(),
            'wajib' => Subject::where('group', 'Wajib')->count(),
            'mulok' => Subject::where('group', 'Muatan Lokal')->count(),
        ];

        return view('admin.subjects.index', compact('subjects', 'stats', 'search', 'group'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        if ($request->has('is_active')) {
            $val = $request->input('is_active');
            $request->merge([
                'is_active' => ($val === '1' || $val === 1 || $val === true || $val === 'true' || $val === 'Aktif') ? 1 : 0
            ]);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code',
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $subject = Subject::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil ditambahkan.',
                'data' => $subject,
            ]);
        }

        return redirect()->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        if ($request->has('is_active')) {
            $val = $request->input('is_active');
            $request->merge([
                'is_active' => ($val === '1' || $val === 1 || $val === true || $val === 'true' || $val === 'Aktif') ? 1 : 0
            ]);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'name' => 'required|string|max:255',
            'group' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $subject->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil diperbarui.',
                'data' => $subject,
            ]);
        }

        return redirect()->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Toggle subject active status.
     */
    public function toggleStatus($id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->is_active = !$subject->is_active;
        $subject->save();

        return response()->json([
            'success' => true,
            'is_active' => $subject->is_active,
            'message' => 'Status mata pelajaran berhasil diubah.',
        ]);
    }

    /**
     * Remove the specified subject.
     */
    public function destroy($id)
    {
        $subject = Subject::withCount('assignments')->findOrFail($id);

        if ($subject->assignments_count > 0) {
            return redirect()->route('subjects.index')
                ->with('error', "Mata pelajaran tidak dapat dihapus karena sedang digunakan dalam {$subject->assignments_count} penugasan guru.");
        }

        $subject->delete();

        return redirect()->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}

