<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Announcement;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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
                  });

            // Filter by user's employee type code
            $userTypeCode = $user->employee && $user->employee->employeeType ? $user->employee->employeeType->code : null;
            if ($userTypeCode) {
                $query->where(function($q) use ($userTypeCode) {
                    $q->where('target_audience', 'global')
                      ->orWhere('target_audience', 'like', "%{$userTypeCode}%");
                });
            } else {
                $query->where('target_audience', 'global');
            }
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
            $file = $request->file('attachment');
            $extension = strtolower($file->extension());
            
            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $manager = new ImageManager(new Driver());
                $image = $manager->decode($file);
                $image->scaleDown(width: 1000);
                
                $filename = 'announcements/' . uniqid() . '.webp';
                $fullPath = storage_path('app/public/' . $filename);
                
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }
                
                $image->save($fullPath, 80);
                $validated['attachment'] = $filename;
            } else {
                $validated['attachment'] = $file->store('announcements', 'public');
            }
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');
        $validated['publish_date'] = $request->input('publish_date', now());

        $announcement = Announcement::create($validated);

        if ($announcement->is_active) {
            // Hanya kirim notifikasi ke user SANS-HRD jika targetnya global, teacher, employee, atau management
            if (in_array($announcement->target_audience, ['global', 'teacher', 'employee', 'management'])) {
                $query = \App\Models\User::where('id', '!=', auth()->id());
                
                if ($announcement->target_audience === 'teacher') {
                    $query->where(function($q) {
                        $q->whereHas('employee.employeeType', function($sq) {
                            $sq->where('code', 'teacher');
                        })->orWhereIn('role', ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);
                    });
                } elseif ($announcement->target_audience === 'employee') {
                    $query->where(function($q) {
                        $q->whereHas('employee.employeeType', function($sq) {
                            $sq->where('code', 'employee');
                        })->orWhereIn('role', ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);
                    });
                } elseif ($announcement->target_audience === 'management') {
                    $query->where(function($q) {
                        $q->whereHas('employee.employeeType', function($sq) {
                            $sq->where('code', 'management');
                        })->orWhereIn('role', ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka']);
                    });
                }
                
                $users = $query->get();
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\NewAnnouncementNotification($announcement));
            }
        }

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function show(Announcement $announcement)
    {
        return view('admin.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        if ($announcement->central_id && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Hanya Super Admin yang dapat memodifikasi pengumuman dari HRD Pusat.');
        }

        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        if ($announcement->central_id && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Hanya Super Admin yang dapat memodifikasi pengumuman dari HRD Pusat.');
        }

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
            
            $file = $request->file('attachment');
            $extension = strtolower($file->extension());
            
            if (in_array($extension, ['png', 'jpg', 'jpeg'])) {
                $manager = new ImageManager(new Driver());
                $image = $manager->decode($file);
                $image->scaleDown(width: 1000);
                
                $filename = 'announcements/' . uniqid() . '.webp';
                $fullPath = storage_path('app/public/' . $filename);
                
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }
                
                $image->save($fullPath, 80);
                $validated['attachment'] = $filename;
            } else {
                $validated['attachment'] = $file->store('announcements', 'public');
            }
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
        if ($announcement->central_id && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Hanya Super Admin yang dapat memodifikasi pengumuman dari HRD Pusat.');
        }

        if ($announcement->attachment) {
            Storage::disk('public')->delete($announcement->attachment);
        }
        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function download(Announcement $announcement)
    {
        if (!$announcement->attachment) {
            abort(404);
        }

        $url = $announcement->attachment;

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            try {
                $filename = basename($url);
                $content = file_get_contents($url);
                if ($content === false) {
                    return redirect($url);
                }
                return response($content)
                    ->header('Content-Type', 'application/octet-stream')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            } catch (\Exception $e) {
                return redirect($url);
            }
        }

        if (!Storage::disk('public')->exists($announcement->attachment)) {
            abort(404);
        }

        return Storage::disk('public')->download($announcement->attachment);
    }
}
