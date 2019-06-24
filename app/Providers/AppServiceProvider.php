<?php

namespace Corp\Providers;

use Illuminate\Support\ServiceProvider;

use Blade;

use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
        // ...
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // @set($i, 10)
        Blade::directive('set', function($exp) {
            list($name, $val) = explode(',', $exp);

            return "<?php $name = $val ?>";
        });

        DB::listen(function($query) {
            echo '<h3>'.$query->sql.'</h3>';
        });
    }
}
