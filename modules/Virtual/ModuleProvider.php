<?php
namespace Modules\Virtual;

use Illuminate\Support\ServiceProvider;
use Modules\ModuleServiceProvider;

class ModuleProvider extends ModuleServiceProvider
{

    public function boot(){
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }


    public static function getAdminMenu()
    {
        return [
            'virtual'=>[
                "position"=>30,
                'url'        => 'admin/module/virtual',
                'title'      => __("Virtual Guide"),
                'icon'       => 'fa fa-hand-o-right',
                'permission' => 'virtual_view',
            ]
        ];
    }
    public static function getTemplateBlocks(){
        return [
            'list_virtuals'=>"\\Modules\\Virtual\\Blocks\\ListVirtuals",
        ];
    }
}
