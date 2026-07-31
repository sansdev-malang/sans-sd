<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = Announcement::latest();

        // Check if user is NOT an admin
        $isAdmin = in_array($user->role, ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);
        
        if (!$isAdmin) {
            $query->where('is_active', true)
                  ->where(function($q) {
                      $q->whereNull('publish_date')
                        ->orWhere('publish_date', '<=', now());
                  })
                  ->where(function($q) {
                      $q->whereNull('expiry_date')
                        ->orWhere('expiry_date', '>=', now());
                  })
                  ->whereIn('target_audience', ['global', 'employee']);
        }

        $announcements = $query->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'target_audience' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'is_active' => 'boolean',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('announcements', 'public');
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['publish_date'] = $request->input('publish_date', now());

        $announcement = Announcement::create($validated);

        if ($announcement->is_active) {
            // Ideally we'd filter users by target_audience (employee vs student vs parent)
            // For now, send to all users except the creator
            $users = \App\Models\User::where('id', '!=', auth()->id())->get();
            \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\NewAnnouncementNotification($announcement));
        }

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'target_audience' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'is_active' => 'boolean',
            'publish_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:publish_date',
        ]);

        if ($request->hasFile('attachment')) {
            if ($announcement->attachment) {
                Storage::disk('public')->delete($announcement->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('announcements', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        if (!$request->filled('publish_date')) {
            unset($validated['publish_date']);
        }

        $announcement->update($validated);

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil diupdate.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->attachment) {
            Storage::disk('public')->delete($announcement->attachment);
        }
        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
