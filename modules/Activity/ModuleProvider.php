<?php
namespace Modules\Activity;
use Modules\ModuleServiceProvider;
use Modules\Activity\Models\Activity;

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
        if(!Activity::isEnable()) return [];
        return [
            'activity'=>[
                "position"=>40,
                'url'        => 'admin/module/activity',
                'title'      => __('Activity'),
                'icon'       => 'fa fa-puzzle-piece',
                'permission' => 'activity_view',
                'children'   => [
                    'add'=>[
                        'url'        => 'admin/module/activity',
                        'title'      => __('All Activitys'),
                        'permission' => 'activity_view',
                    ],
                    'create'=>[
                        'url'        => 'admin/module/activity/create',
                        'title'      => __('Add new Activity'),
                        'permission' => 'activity_create',
                    ],
                    'attribute'=>[
                        'url'        => 'admin/module/activity/attribute',
                        'title'      => __('Attributes'),
                        'permission' => 'activity_manage_attributes',
                    ],
                ]
            ]
        ];
    }

    public static function getBookableServices()
    {
        if(!Activity::isEnable()) return [];
        return [
            'activity'=>Activity::class
        ];
    }

    public static function getMenuBuilderTypes()
    {
        if(!Activity::isEnable()) return [];
        return [
            'activity'=>[
                'class' => Activity::class,
                'name'  => __("Activity"),
                'items' => Activity::searchForMenu(),
                'position'=>41
            ]
        ];
    }


    public static function getUserMenu()
    {
        if(!Activity::isEnable()) return [];
        return [
            'activity' => [
                'url'   => route('activity.vendor.index'),
                'title'      => __("Manage Activity"),
                'icon'       => Activity::getServiceIconFeatured(),
                'position'   => 30,
                'permission' => 'activity_view',
                'children' => [
                    [
                        'url'   => route('activity.vendor.index'),
                        'title'  => __("All Activity"),
                    ],
                    [
                        'url'   => route('activity.vendor.create'),
                        'title'      => __("Add Activity"),
                        'permission' => 'activity_create',
                    ],
                    [
                        'url'   => route('activity.vendor.booking_report'),
                        'title'      => __("Booking Report"),
                        'permission' => 'activity_view',
                    ],
                ]
            ],
        ];
    }

    public static function getTemplateBlocks(){
        if(!Activity::isEnable()) return [];
        return [
            'form_search_activity'=>"\\Modules\\Activity\\Blocks\\FormSearchActivity",
            'list_activity'=>"\\Modules\\Activity\\Blocks\\ListActivity",
        ];
    }
}
