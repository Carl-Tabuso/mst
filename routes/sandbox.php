<?php

use App\Enums\UserRole;
use App\Mail\TestMail;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// for sandboxing/testing routes, do them here  - Carl

Route::get('test', function () {
    $dispatchers = User::role(UserRole::Dispatcher)->get()->pluck('email')->toArray();
    $teamLeaders = User::role(UserRole::TeamLeader)->get()->pluck('email')->toArray();
    // $head        = User::role(UserRole::HeadFrontliner)->first()->email;
    $itAdmins    = User::role(UserRole::ITAdmin)->get()->pluck('email')->toArray();
    $consultants = User::role(UserRole::Consultant)->get()->pluck('email')->toArray();
    $hrs         = User::role(UserRole::HumanResource)->get()->pluck('email')->toArray();
    $regulars    = User::role(UserRole::Regular)->get()->pluck('email')->toArray();
    $frontliners = User::role(UserRole::Frontliner)->get()->pluck('email')->toArray();

    dd([
        'dispatchers'     => $dispatchers,
        'team leaders'    => $teamLeaders,
        // 'head'            => $head,
        'it admins'       => $itAdmins,
        'consultants'     => $consultants,
        'human resources' => $hrs,
        'regulars'        => $regulars,
        'frontliners'     => $frontliners,
    ]);
});

Route::get('test-mail', function () {
    return new TestMail;
});
