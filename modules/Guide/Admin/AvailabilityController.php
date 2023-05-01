<?php
namespace Modules\Guide\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;

class AvailabilityController extends \Modules\Guide\Controllers\AvailabilityController
{
    protected $indexView = 'Guide::admin.room.availability';

    public function __construct()
    {
        parent::__construct();
        $this->setActiveMenu('admin/module/guide');
        $this->middleware('dashboard');
    }

    protected function hasGuidePermission($guide_id = false){
        if(empty($guide_id)) return false;

        $guide = $this->guideClass::find($guide_id);
        if(empty($guide)) return false;

        if(!$this->hasPermission('guide_manage_others') and $guide->create_user != Auth::id()){
            return false;
        }

        $this->currentGuide = $guide;
        return true;
    }
}