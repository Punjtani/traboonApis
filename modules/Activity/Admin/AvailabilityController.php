<?php
namespace Modules\Activity\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\AdminController;

class AvailabilityController extends \Modules\Activity\Controllers\AvailabilityController
{
    protected $indexView = 'Activity::admin.room.availability';

    public function __construct()
    {
        parent::__construct();
        $this->setActiveMenu('admin/module/activity');
        $this->middleware('dashboard');
    }

    protected function hasActivityPermission($activity_id = false){
        if(empty($activity_id)) return false;

        $activity = $this->activityClass::find($activity_id);
        if(empty($activity)) return false;

        if(!$this->hasPermission('activity_manage_others') and $activity->create_user != Auth::id()){
            return false;
        }

        $this->currentActivity = $activity;
        return true;
    }
}