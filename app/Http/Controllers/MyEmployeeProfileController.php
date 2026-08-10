<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MyEmployeeProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        if (!$user || !$user->employee_id) {
            return redirect()->back()->with('error', 'Akun Anda belum dihubungkan dengan data pegawai.');
        }

        $employee = Employee::with('employeeType')->findOrFail($user->employee_id);
        $employeeTypes = \App\Models\EmployeeType::all();

        return view('profile.employee', compact('employee', 'employeeTypes'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->employee_id) {
            return redirect()->back()->with('error', 'Akun Anda belum dihubungkan dengan data pegawai.');
        }

        $employee = Employee::findOrFail($user->employee_id);

                $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
            'front_title.max' => 'Gelar depan tidak boleh lebih dari 50 karakter.',
            'back_title.max' => 'Gelar belakang tidak boleh lebih dari 50 karakter.',
            'birth_place.max' => 'Tempat lahir terlalu panjang (maks. 255 karakter).',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'address.max' => 'Alamat terlalu panjang (maks. 255 karakter).',
            'phone.max' => 'Nomor HP tidak boleh lebih dari 20 karakter.',
            'nik.max' => 'NIK tidak boleh lebih dari 255 karakter.',
            'niy.max' => 'NIY tidak boleh lebih dari 255 karakter.',
            'nuptk.max' => 'NUPTK tidak boleh lebih dari 255 karakter.',
            'no_ukg.max' => 'No UKG tidak boleh lebih dari 255 karakter.',
            'nrg.max' => 'NRG tidak boleh lebih dari 255 karakter.',
            'last_education.max' => 'Pendidikan terakhir terlalu panjang.',
            'major.max' => 'Jurusan terlalu panjang.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
        ];

        $validated = $request->validate([
            'front_title' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'back_title' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:Male,Female,L,P',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|max:255',
            'niy' => 'nullable|string|max:255',
            'nuptk' => 'nullable|string|max:255',
            'no_ukg' => 'nullable|string|max:255',
            'nrg' => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'last_education' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ], $messages);

        if ($request->hasFile('photo')) {
            $manager = new ImageManager(new Driver());
            $image = $manager->decode($request->file('photo'));
            $image->scaleDown(width: 800);
            
            $filename = 'photos/' . uniqid() . '.webp';
            $fullPath = storage_path('app/public/' . $filename);
            
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0755, true);
            }
            
            $image->save($fullPath, 80);
            
            if ($employee->photo && file_exists(storage_path('app/public/' . $employee->photo))) {
                unlink(storage_path('app/public/' . $employee->photo));
            }
            
            $validated['photo'] = $filename;
        }

        $employee->update($validated);

        // Sync with associated User account
        if ($employee->user) {
            $employee->user->update([
                'name' => $employee->name, // Formatted name with titles
                'email' => $employee->email,
            ]);
        }

        return redirect()->route('my-employee-profile.edit')->with('success', 'Profil pegawai berhasil diperbarui.');
    }
}