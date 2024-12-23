<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Notifications\ResetPassword;

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
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return match (true) {
                $user instanceof Admin => 'http://admin.our-website/reset-password' . '?token=' . $token . '&email=' . urlencode($user->email),
                $user instanceof User => 'http://our-website/reset-password' . '?token=' . $token . '&email=' . urlencode($user->email),
                    // other user types
                default => throw new \Exception("Invalid user type"),
            };
        });
    }
}
