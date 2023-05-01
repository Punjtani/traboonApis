<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Plugins\Widgets\Blocks;

/**
 * Description of MobileAppWidget
 *
 * @author Monetory
 */

use Modules\Template\Blocks\BaseBlock;
use Modules\Location\Models\Location;
use Modules\Media\Helpers\FileHelper;
class MobileAppWidget  extends BaseBlock{
    //put your code here
    function __construct()
    {
        $this->setOptions([
            'settings' => [
                [
                    'id'        => 'title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title')
                ],
                [
                    'id'        => 'sub_title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Sub Title')
                ],
                [
                    'id'        => 'link_title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title Link More')
                ],
                [
                    'id'        => 'link_more',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Link More')
                ],
            ]
        ]);
    }

    public function getName()
    {
        return __('Mobile App Widget');
    }

    public function content($model = [])
    {
        return view('Widgets::mobile.index', $model);
    }
}
