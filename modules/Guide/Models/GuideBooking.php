<?php

namespace Modules\Guide\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Modules\Booking\Models\Bookable;
use Modules\Booking\Models\Booking;
use Modules\Core\Models\SEO;
use Modules\Media\Helpers\FileHelper;
use Modules\Review\Models\Review;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guide\Models\GuideTranslation;
use Modules\User\Models\UserWishList;

class GuideBooking extends Bookable
{
    protected $table = 'bravo_guide_bookings';

    public static function getTableName()
    {
        return with(new static)->table;
    }

    public function scopeInRange($query,$start,$end){
        return $query->where('bravo_guide_bookings.start_date','<=',$end)->where('bravo_guide_bookings.end_date','>=',$start);
    }


    public function scopeActive($query){
        return $query->join('bravo_bookings',function ($join){
           $join->on('bravo_bookings.id','=',$this->table.'.booking_id');
        })->whereNotIn('bravo_bookings.status',Booking::$notAcceptedStatus);
    }


    public static function getByBookingId($id){
        return parent::query()->where([
            'booking_id'=>$id
        ])->get();
    }
}
