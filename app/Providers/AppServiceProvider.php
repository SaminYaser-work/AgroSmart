<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Foundation\Vite;
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
//        Filament::registerScripts([
//            'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
//        ], true);

        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/filament.css');
            Filament::registerRenderHook(
                'head.end',
                static fn()=>(new Vite)(['resources/css/app.css'])
            );
            Filament::registerNavigationGroups([
                'Crop',
                'Livestock',
                'Sales',
                'Supply',
                'HCM',
                'Inventory'
            ]);
        });
    }
}
