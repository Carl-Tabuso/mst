<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'user'       => 'App\Models\User',
            'form4'      => 'App\Models\Form4',
            'form5'      => 'App\Models\Form5',
            'it_service' => 'App\Models\ITService',
            'job_order'  => 'App\Models\JobOrder',
        ]);

        JsonResource::withoutWrapping();

        Gate::define('viewPulse', fn (User $user) => $user->hasRole(UserRole::ITAdmin));
        Gate::define('viewActivityLogs', [ActivityLogPolicy::class, 'viewAny']);
        Gate::define('viewReports', [ReportPolicy::class, 'viewAny']);
    }
}
