<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // 상용 환경일 때 모든 URL 생성을 HTTPS로 강제합니다.
        if (app()->environment('live')) {
            URL::forceScheme('https');
        }
    }
}
