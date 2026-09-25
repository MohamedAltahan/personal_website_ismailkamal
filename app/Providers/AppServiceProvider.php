<?php

namespace App\Providers;

use App\Support\Settings;
use App\View\Composers\SiteComposer;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('components.pagination');

        ResetPassword::createUrlUsing(fn ($user, string $token) => route('admin.password.reset', [
            'token' => $token,
            'email' => $user->getEmailForPasswordReset(),
        ]));

        View::composer(['layouts.site', 'site.*', 'blocks.*', 'errors.*'], SiteComposer::class);
    }
}
