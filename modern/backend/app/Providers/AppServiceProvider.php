<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Gate::define('access.manage', fn (User $user): bool => $user->isSystemAdmin());
        foreach (array_keys(config('cms.permissions')) as $permission) {
            Gate::define($permission, fn (User $user): bool => in_array($permission, $user->permissions(), true));
        }
        RateLimiter::for('login', fn (Request $request): array => [
            Limit::perMinute(5)->by('email:'.hash('sha256', strtolower((string) $request->input('email')).'|'.$request->ip())),
            Limit::perMinute(30)->by('ip:'.$request->ip()),
        ]);
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)
            ->by($request->user()?->id ?: $request->ip()));
    }
}
