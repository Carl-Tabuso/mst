<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ArchivedUserSeeder extends Seeder
{
    public function run(): void
    {
        activity()->disableLogging();

        $validRoles = array_diff(array_map(
            fn ($role) => $role->value, UserRole::cases()
        ), [UserRole::HeadFrontliner->value]);

        DB::transaction(function () use ($validRoles) {
            User::factory()
                ->count(20)
                ->trashed()
                ->create()
                ->each(fn (User $user) => $user->assignRole(Arr::random($validRoles)));
        });
    }
}
