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
