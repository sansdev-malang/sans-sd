<?php

namespace App\Http\Controllers;

use App\Models\ZktecoDevice;
use Illuminate\Http\Request;

class ZktecoDeviceController extends Controller
{
    /**
     * Display a listing of the Zkteco devices.
     */
    public function index()
    {
        $devices = ZktecoDevice::orderBy('name')->get();
        return view('admin.zkteco-devices.index', compact('devices'));
    }

    /**
     * Store a newly created Zkteco device in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'model_name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $validated['is_online'] = false; // default false until connection verified

        ZktecoDevice::create($validated);

        return redirect()->route('zkteco-devices.index')
            ->with('success', 'Perangkat ZKTeco baru berhasil ditambahkan.');
    }

    /**
     * Update the specified Zkteco device in storage.
     */
    public function update(Request $request, ZktecoDevice $zktecoDevice)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'model_name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $zktecoDevice->update($validated);

        return redirect()->route('zkteco-devices.index')
            ->with('success', 'Perangkat ZKTeco berhasil diperbarui.');
    }

    /**
     * Remove the specified Zkteco device from storage.
     */
    public function destroy(ZktecoDevice $zktecoDevice)
    {
        $zktecoDevice->delete();

        return redirect()->route('zkteco-devices.index')
            ->with('success', 'Perangkat ZKTeco berhasil dihapus.');
    }

    /**
     * Perform socket connection test (ping) on the device.
     */
    public function ping(ZktecoDevice $zktecoDevice)
    {
        $connection = @fsockopen($zktecoDevice->ip_address, $zktecoDevice->port, $errno, $errstr, 1.0);
        $isOnline = is_resource($connection);
        if ($isOnline) {
            fclose($connection);
        }

        $zktecoDevice->update(['is_online' => $isOnline]);

        return response()->json([
            'success' => true,
            'is_online' => $isOnline,
            'message' => $isOnline ? 'Koneksi Berhasil (Online)' : 'Koneksi Gagal (Offline)'
        ]);
    }
}
