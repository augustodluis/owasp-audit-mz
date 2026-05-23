<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@audit-mz.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'auditor@audit-mz.local'],
            [
                'name' => 'Augusto Luis',
                'password' => Hash::make('auditor12345'),
                'role' => 'auditor',
            ]
        );
    }
}
