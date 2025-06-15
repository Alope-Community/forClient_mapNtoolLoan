<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'nama' => 'Admin User',
            'gender' => 'L',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('foobarrr'),
        ]);

        $admin->assignRole('admin');

        $karyawan = User::create([
            'nama' => 'Karyawan A',
            'gender' => 'P',
            'email' => 'karyawan@gmail.com',
            'password' => Hash::make('foobarrr'),
        ]);

        $karyawan->assignRole('karyawan');
        
        $kepala = User::create([
            'nama' => 'Kepala Bidang A',
            'gender' => 'P',
            'email' => 'kepalabidang@gmail.com',
            'password' => Hash::make('foobarrr'),
        ]);

        $kepala->assignRole('kepala');
    }
}
