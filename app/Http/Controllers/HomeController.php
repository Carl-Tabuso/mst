<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Enums\UserRole;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $userRole = $user->getRoleNames()->first();

        $component = match (UserRole::from($userRole)) {
            UserRole::HeadFrontliner => 'home/1',
            UserRole::ITAdmin => 'home/2',
            default => 'home/3',
        };

        $hour = now()->hour;

        $currentHour = (object) [
            'greeting' => '',
            'illustration' => '',
        ];

        $employeeFirstName = $user->employee->first_name;

        if ($hour < 12) {
            $currentHour->greeting = "Good Morning, {$employeeFirstName}!";
            $currentHour->illustration = 'greetings/morning-illustration.svg';
        } elseif ($hour < 18) {
            $currentHour->greeting = "Good Afternoon, {$employeeFirstName}!";
            $currentHour->illustration = 'greetings/afternoon-illustration.svg';
        } else {
            $currentHour->greeting = "Good Evening, {$employeeFirstName}!";
            $currentHour->illustration = 'greetings/evening-illustration.svg';
        }

        return Inertia::render($component, [
            'greeting' => $currentHour->greeting,
            'illustration' => $currentHour->illustration,
        ]);
    }
}
