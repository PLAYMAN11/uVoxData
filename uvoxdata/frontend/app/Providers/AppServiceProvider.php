<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Ensure Vite assets use root-relative paths so they work
        // through any proxy/tunnel (ngrok, cloudflare, etc.)
        Vite::createAssetPathsUsing(fn (string $path) => '/' . $path);
    }
}
