<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@unishams.edu.my'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('Admin@12345'),
                'is_admin' => true,
            ]
        );
    }
}
