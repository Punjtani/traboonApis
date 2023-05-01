<?php

namespace  Modules\Activity;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id'   => 'activity',
                'title' => __("Activity Settings"),
                'position'=>20,
                'view'=>"Activity::admin.settings.activity",
                "keys"=>[
                    'activity_disable',
                    'activity_page_search_title',
                    'activity_page_search_banner',
                    'activity_layout_search',
                    'activity_layout_item_search',
                    'activity_attribute_show_in_listing_page',
                    'activity_location_search_style',

                    'activity_enable_review',
                    'activity_review_approved',
                    'activity_enable_review_after_booking',
                    'activity_review_number_per_page',
                    'activity_review_stats',

                    'activity_page_list_seo_title',
                    'activity_page_list_seo_desc',
                    'activity_page_list_seo_image',
                    'activity_page_list_seo_share',

                    'activity_booking_buyer_fees',
                    'activity_vendor_create_service_must_approved_by_admin',
                    'activity_allow_vendor_can_change_their_booking_status',
                    'activity_map_search_fields',

                    'activity_allow_review_after_making_completed_booking',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}