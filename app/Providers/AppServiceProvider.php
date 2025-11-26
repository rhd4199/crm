<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::directive('waLink', function ($expression) {
            // $expression = nomor hp mentah
            return "<?php
                \$__raw = $expression;
                \$__num = preg_replace('/[^0-9]/', '', \$__raw ?? '');
                if (str_starts_with(\$__num, '0')) {
                    \$__num = '62' . substr(\$__num, 1);
                }
                \$__url = 'https://wa.me/' . \$__num;
                echo \$__url;
            ?>";
        });
    }
}
