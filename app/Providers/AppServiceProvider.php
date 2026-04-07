<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

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
        JsonResource::withoutWrapping();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        Gate::define('admin', function(User $user) {
            return $user->role == 'admin';
        });
        Gate::define('verifier', function(User $user) {
            return $user->role == 'verifier';
        });
        Gate::define('analyst', function(User $user) {
            return $user->role == 'analyst';
        });
        Gate::define('manager', function(User $user) {
            return $user->role == 'manager';
        });
        Gate::define('applicant', function(User $user) {
            return $user->role == 'applicant';
        });
    }
}
