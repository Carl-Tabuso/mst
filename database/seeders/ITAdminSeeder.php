<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class ITAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'email'    => config('auth.it_admin_email'),
            'password' => config('auth.it_admin_password'),
        ])
            ->assignRole(UserRole::ITAdmin);
    }
}
