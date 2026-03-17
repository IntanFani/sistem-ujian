<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    
    public function boot(): void
    {
        // Paksa Laravel pakai style Bootstrap 5 untuk pagination
        Paginator::useBootstrapFive();
    }
}

