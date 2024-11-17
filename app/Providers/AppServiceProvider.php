<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Pagination\Paginator;
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
        // Instead of redirection to index home page, redirect to chat index
        RedirectIfAuthenticated::redirectUsing(function () {
            return route("chats.index");
        });

        // Use Bootstrap pagination styling
        Paginator::useBootstrapFive();
    }
}
