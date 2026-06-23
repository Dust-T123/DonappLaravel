<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Configuración global de Eloquent: deshabilitar timestamps automáticos
        // Los modelos del proyecto usan $timestamps = false individualmente
    }
}
