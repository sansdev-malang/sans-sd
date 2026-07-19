<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Directive untuk role tertentu
        Blade::if('role', function ($role) {
            return Auth::check() && Auth::user()->role === $role;
        });

        // Directive untuk selain role tertentu
        Blade::if('notrole', function ($role) {
            return Auth::check() && Auth::user()->role !== $role;
        });

        // Directive untuk cek banyak role sekaligus
        Blade::if('anyrole', function (array $roles) {
            return Auth::check() && in_array(Auth::user()->role, $roles);
        });

        // Directive untuk selain banyak role sekaligus
        Blade::if('norole', function (array $roles) {
            return Auth::check() && ! in_array(Auth::user()->role, $roles);
        });

        // Share pending leaves & user notifications with header component
        view()->composer('partials.admin.header', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $schoolUnit = config('app.school_unit');
                
                if (in_array($user->role, ['super_admin', 'admin_sd', 'admin_paud', 'admin_smp', 'kepala_sekolah', 'waka'])) {
                    $pendingLeavesQuery = \App\Models\LeaveRequest::with('employee')->where('status', 'Pending');
                    if ($schoolUnit) {
                        $pendingLeavesQuery->whereHas('employee', function ($q) use ($schoolUnit) {
                            $q->where('unit', $schoolUnit);
                        });
                    }
                    $pendingLeavesCount = (clone $pendingLeavesQuery)->count();
                    $pendingLeaves = $pendingLeavesQuery->latest()->limit(5)->get();
                    
                    $view->with(compact('pendingLeaves', 'pendingLeavesCount'));
                } else {
                    $myNotifications = \App\Models\LeaveRequest::where('employee_id', $user->employee_id)
                        ->whereIn('status', ['Approved', 'Rejected'])
                        ->latest()
                        ->limit(5)
                        ->get();
                    
                    $view->with(compact('myNotifications'));
                }
            }
        });
    }
}


// @anyrole(['guru','siswa'])
//     <p>Menu ini tampil untuk Guru dan Siswa.</p>
// @endanyrole

// @role('admin')
//     <p>Menu khusus Admin.</p>
// @endrole

// @norole(['guru','siswa'])
//     <p>Menu ini tampil untuk semua role kecuali Guru dan Siswa.</p>
// @endnorole

// @notrole('admin')
//     <p>Menu untuk semua role selain Admin.</p>
// @endnotrole
