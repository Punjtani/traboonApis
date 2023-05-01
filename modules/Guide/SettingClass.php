<?php

namespace  Modules\Guide;

use Modules\Core\Abstracts\BaseSettingsClass;
use Modules\Core\Models\Settings;

class SettingClass extends BaseSettingsClass
{
    public static function getSettingPages()
    {
        return [
            [
                'id'   => 'guide',
                'title' => __("Guide Settings"),
                'position'=>20,
                'view'=>"Guide::admin.settings.guide",
                "keys"=>[
                    'guide_disable',
                    'guide_page_search_title',
                    'guide_page_search_banner',
                    'guide_layout_search',
                    'guide_layout_item_search',
                    'guide_attribute_show_in_listing_page',
                    'guide_location_search_style',

                    'guide_enable_review',
                    'guide_review_approved',
                    'guide_enable_review_after_booking',
                    'guide_review_number_per_page',
                    'guide_review_stats',

                    'guide_page_list_seo_title',
                    'guide_page_list_seo_desc',
                    'guide_page_list_seo_image',
                    'guide_page_list_seo_share',

                    'guide_booking_buyer_fees',
                    'guide_vendor_create_service_must_approved_by_admin',
                    'guide_allow_vendor_can_change_their_booking_status',
                    'guide_map_search_fields',

                    'guide_allow_review_after_making_completed_booking',
                ],
                'html_keys'=>[

                ]
            ]
        ];
    }
}