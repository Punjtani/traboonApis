<?php
namespace Plugins\Widgets;

use Illuminate\Support\ServiceProvider;
use Modules\ModuleServiceProvider;
//use Plugins\Wid\Tour\Models\Tour;

class ModuleProvider extends ModuleServiceProvider
{
    public function boot()
    {
        //$this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->register(RouterServiceProvider::class);
    }

    

    public static function getTemplateBlocks(){

        return [
            'weather_widget'=>"\\Plugins\\Widgets\\Blocks\\WeatherWidget",
            'mobile_widget'=>"\\Plugins\\Widgets\\Blocks\\MobileAppWidget",
        ];
    }
}
