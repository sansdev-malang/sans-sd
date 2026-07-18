<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ZktecoDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZktecoDeviceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_zkteco_devices()
    {
        $response = $this->get(route('zkteco-devices.index'));
        $response->assertRedirect('/login');
    }

    public function test_authorized_user_can_access_zkteco_devices_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('zkteco-devices.index'));
        $response->assertStatus(200);
    }

    public function test_authorized_user_can_create_zkteco_device()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('zkteco-devices.store'), [
            'name' => 'Mesin Uji',
            'ip_address' => '192.168.1.210',
            'port' => 4370,
            'model_name' => 'ZKTeco LX50',
            'location' => 'Lobby Utama',
        ]);

        $response->assertRedirect(route('zkteco-devices.index'));
        $this->assertDatabaseHas('zkteco_devices', [
            'name' => 'Mesin Uji',
            'ip_address' => '192.168.1.210',
        ]);
    }

    public function test_authorized_user_can_ping_device()
    {
        $user = User::factory()->create();
        $device = ZktecoDevice::create([
            'name' => 'Mesin Uji',
            'ip_address' => '127.0.0.1', // localhost will fail to connect on port 4370 typically, or we just assert json structure
            'port' => 4370,
            'model_name' => 'ZKTeco LX50',
            'location' => 'Lobby Utama',
        ]);

        $response = $this->actingAs($user)->post(route('zkteco-devices.ping', $device->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'is_online',
            'message'
        ]);
    }
}
