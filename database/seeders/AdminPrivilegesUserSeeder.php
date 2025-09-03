<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPrivilegesUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        activity()->disableLogging();

        DB::transaction(function () {
            $this->createITAdmin();
            $this->createHeadFrontliner();
        });

        DB::enableQueryLog();
    }

    private function createITAdmin(): void
    {
        User::factory()->create([
            'email'    => config('auth.it_admin_email'),
            'password' => config('auth.it_admin_password'),
        ])
            ->assignRole(UserRole::ITAdmin);
    }

    private function createHeadFrontliner(): void
    {
        User::factory()
            ->create(['email' => 'headfrontliner@gmail.com'])
            ->assignRole(UserRole::HeadFrontliner);
    }
}
