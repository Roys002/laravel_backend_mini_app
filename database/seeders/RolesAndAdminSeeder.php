<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // buat admin test (jika belum ada)
        $adminEmail = 'admin@example.com';
        $admin = User::where('email', $adminEmail)->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('password123'), // ganti di production
            ]);
        }

        // assign role admin
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
