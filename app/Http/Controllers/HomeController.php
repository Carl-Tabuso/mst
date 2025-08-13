<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $userRole = $user->getRoleNames()->first();

        $component = match (UserRole::from($userRole)) {
            UserRole::HeadFrontliner => 'home/1/Index',
            UserRole::ITAdmin        => 'home/2/Index',
            default                  => 'home/3/Index',
        };

        $hour = now()->hour;

        $currentHour = (object) [
            'dayPart'      => '',
            'illustration' => '',
        ];

        if ($hour < 12) {
            $currentHour->dayPart      = 'morning';
            $currentHour->illustration = 'greetings/morning-illustration.svg';
        } elseif ($hour < 18) {
            $currentHour->dayPart      = 'afternoon';
            $currentHour->illustration = 'greetings/afternoon-illustration.svg';
        } else {
            $currentHour->dayPart      = 'evening';
            $currentHour->illustration = 'greetings/evening-illustration.svg';
        }

        return Inertia::render($component, [
            'dayPart'      => $currentHour->dayPart,
            'illustration' => $currentHour->illustration,
        ]);
    }
}
