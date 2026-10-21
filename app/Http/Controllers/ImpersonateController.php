<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Start impersonating a user.
     */
    public function enter(Request $request, $id)
    {
        $currentUser = Auth::user();

        // Only super_admin can start impersonation
        if (!$currentUser || !$currentUser->hasRole('super_admin')) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang dapat menggunakan fitur ini.');
        }

        $targetUser = User::findOrFail($id);

        // Cannot impersonate oneself
        if ($currentUser->id === $targetUser->id) {
            return back()->with('error', 'Anda tidak dapat melakukan impersonasi ke akun Anda sendiri.');
        }

        $originalAdminId = session()->get('impersonator_id', $currentUser->id);
        $originalAdminName = session()->get('impersonator_name', $currentUser->name);

        Auth::login($targetUser);

        // Ensure session persists under new authenticated session
        session()->put('impersonator_id', $originalAdminId);
        session()->put('impersonator_name', $originalAdminName);

        return redirect()->route('dashboard')->with('success', "Mode Impersonasi Aktif: Anda sekarang masuk sebagai {$targetUser->name}.");
    }

    /**
     * Leave impersonation mode and return to original admin.
     */
    public function leave(Request $request)
    {
        $adminId = session()->get('impersonator_id');

        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin) {
                session()->forget(['impersonator_id', 'impersonator_name']);
                Auth::login($admin);
                return redirect()->route('users.index')->with('success', "Berhasil kembali ke akun Super Admin ({$admin->name}).");
            }
        }

        session()->forget(['impersonator_id', 'impersonator_name']);
        return redirect()->route('dashboard');
    }
}
