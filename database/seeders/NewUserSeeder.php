<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewUserSeeder extends Seeder
{
    public function run(): void
    {
        activity()->disableLogging();

        User::factory()
            ->unverified()
            ->noRememberToken()
            ->create(['email' => 'tabusocarl16@gmail.com'])
            ->assignRole(UserRole::ITAdmin);
    }
}
