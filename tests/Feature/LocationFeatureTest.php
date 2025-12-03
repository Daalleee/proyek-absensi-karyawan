<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LocationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_location()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Membuat lokasi baru
        $response = $this->post('/locations', [
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Sudirman No. 1 Jakarta',
            'latitude' => -6.175394,
            'longitude' => 106.827060,
            'radius' => 100,
            'description' => 'Lokasi utama kantor',
        ]);

        $response->assertRedirect('/locations');
        
        // Memastikan lokasi telah dibuat
        $this->assertDatabaseHas('locations', [
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Sudirman No. 1 Jakarta',
            'latitude' => -6.175394,
            'longitude' => 106.827060,
            'radius' => 100,
            'description' => 'Lokasi utama kantor',
        ]);
    }

    public function test_admin_can_update_location()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat lokasi
        $location = Location::factory()->create([
            'name' => 'Lokasi Lama',
            'address' => 'Alamat Lama',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin2@example.com',
            'password' => 'password',
        ]);

        // Update lokasi
        $response = $this->put("/locations/{$location->id}", [
            'name' => 'Lokasi Baru',
            'address' => 'Alamat Baru',
            'latitude' => -6.175394,
            'longitude' => 106.827060,
            'radius' => 200,
            'description' => 'Lokasi yang telah diperbarui',
        ]);

        $response->assertRedirect('/locations');
        
        // Memastikan lokasi telah diupdate
        $this->assertDatabaseHas('locations', [
            'id' => $location->id,
            'name' => 'Lokasi Baru',
            'address' => 'Alamat Baru',
            'latitude' => -6.175394,
            'longitude' => 106.827060,
            'radius' => 200,
            'description' => 'Lokasi yang telah diperbarui',
        ]);
    }

    public function test_admin_can_delete_location()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin3@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat lokasi
        $location = Location::factory()->create([
            'name' => 'Lokasi untuk Dihapus',
            'address' => 'Alamat untuk Dihapus',
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin3@example.com',
            'password' => 'password',
        ]);

        // Hapus lokasi
        $response = $this->delete("/locations/{$location->id}");
        $response->assertRedirect('/locations');
        
        // Memastikan lokasi telah dihapus
        $this->assertDatabaseMissing('locations', [
            'id' => $location->id,
        ]);
    }
    
    public function test_admin_can_toggle_location_status()
    {
        // Membuat user admin
        $admin = User::factory()->create([
            'email' => 'admin4@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Membuat lokasi aktif
        $location = Location::factory()->create([
            'name' => 'Lokasi Aktif',
            'address' => 'Alamat Lokasi',
            'is_active' => true,
        ]);

        // Login sebagai admin
        $this->post('/login', [
            'email' => 'admin4@example.com',
            'password' => 'password',
        ]);

        // Toggle status lokasi
        $response = $this->post("/locations/{$location->id}/toggle");
        $response->assertRedirect('/locations');
        
        // Memastikan status lokasi telah diubah
        $location->refresh();
        $this->assertFalse($location->is_active);
    }
    
    public function test_employee_cannot_access_location_management()
    {
        // Membuat user employee
        $user = User::factory()->create([
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
        ]);

        // Login sebagai employee
        $this->post('/login', [
            'email' => 'employee@example.com',
            'password' => 'password',
        ]);

        // Mencoba mengakses halaman lokasi - harus ditolak
        $response = $this->get('/locations');
        $response->assertRedirect('/dashboard'); // Redirect karena middleware admin
    }
}