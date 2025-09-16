<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArchivedUserSeeder extends Seeder
{
    public function run(): void
    {
        activity()->disableLogging();

        DB::transaction(function () {
            User::factory()
                ->count(20)
                ->trashed()
                ->create()
                ->each(fn (User $user) => $user->assignRole(array_rand(UserRole::cases())));
        });
    }
}
