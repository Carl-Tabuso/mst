<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ActivityLogName;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        Inertia::clearHistory();

        activity()
            ->useLog(ActivityLogName::Auth->value)
            ->causedBy($request->user())
            ->event('login')
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("{$request->user()->employee->full_name} has logged in to session.");

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        activity()
            ->useLog(ActivityLogName::Auth->value)
            ->causedBy($request->user())
            ->event('logout')
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log("{$request->user()->employee->full_name} has logged out of session.");

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        Inertia::clearHistory();

        return redirect('/login');
    }
}
