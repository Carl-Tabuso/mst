<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\EmployeeRating;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\EmployeeRatingPolicy;
use App\Policies\ReportPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
            'user'                  => 'App\Models\User',
            'form4'                 => 'App\Models\Form4',
            'form5'                 => 'App\Models\Form5',
            'it_service'            => 'App\Models\ITService',
            'job_order'             => 'App\Models\JobOrder',
            'initial_onsite_report' => 'App\Models\InitialOnsiteReport',
            'final_onsite_report'   => 'App\Models\FinalOnsiteReport',
            'employee_rating'       => 'App\Models\EmployeeRating',
            'truck'                 => 'App\Models\Truck',
            'job_order_correction'  => 'App\Models\JobOrderCorrection',
        ]);

        JsonResource::withoutWrapping();

        Password::defaults(
            fn() => Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
                ->rules(['not_regex:/\s/'])
        );
        Gate::define('viewPulse', fn(User $user) => $user->hasRole(UserRole::ITAdmin));
        Gate::define('viewActivityLogs', [ActivityLogPolicy::class, 'viewAny']);
        Gate::define('viewReports', [ReportPolicy::class, 'viewAny']);

        Gate::policy(EmployeeRating::class, EmployeeRatingPolicy::class);
    }
}
