<?php
namespace Modules\Guide;
use Modules\ModuleServiceProvider;
use Modules\Guide\Models\Guide;

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
        if(!Guide::isEnable()) return [];
        return [
            'guide'=>[
                "position"=>40,
                'url'        => 'admin/module/guide',
                'title'      => __('Guide'),
                'icon'       => 'fa fa-map-signs',
                'permission' => 'guide_view',
                'children'   => [
                    'add'=>[
                        'url'        => 'admin/module/guide',
                        'title'      => __('All Guides'),
                        'permission' => 'guide_view',
                    ],
                    'create'=>[
                        'url'        => 'admin/module/guide/create',
                        'title'      => __('Add new Guide'),
                        'permission' => 'guide_create',
                    ],
                    'attribute'=>[
                        'url'        => 'admin/module/guide/attribute',
                        'title'      => __('Attributes'),
                        'permission' => 'guide_manage_attributes',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Guide::isEnable()) return [];
        return [
            'guide'=>Guide::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Guide::isEnable()) return [];
        return [
            'guide'=>[
                'class' => Guide::class,
                'name'  => __("Guide"),
                'items' => Guide::searchForMenu(),
                'position'=>41
            ]
        ];
    }


    public static function getUserMenu()
    {
        if(!Guide::isEnable()) return [];
        return [
            'guide' => [
                'url'   => route('guide.vendor.index'),
                'title'      => __("Manage Guide"),
                'icon'       => Guide::getServiceIconFeatured(),
                'position'   => 30,
                'permission' => 'guide_view',
                'children' => [
                    [
                        'url'   => route('guide.vendor.index'),
                        'title'  => __("All Guide"),
                    ],
                    [
                        'url'   => route('guide.vendor.create'),
                        'title'      => __("Add Guide"),
                        'permission' => 'guide_create',
                    ],
                    [
                        'url'   => route('guide.vendor.booking_report'),
                        'title'      => __("Booking Report"),
                        'permission' => 'guide_view',
                    ],
                ]
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Guide::isEnable()) return [];
        return [
            'form_search_guide'=>"\\Modules\\Guide\\Blocks\\FormSearchGuide",
            'list_guide'=>"\\Modules\\Guide\\Blocks\\ListGuide",
        ];
    }
}
