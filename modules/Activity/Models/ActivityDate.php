<?php
namespace Modules\Activity\Models;

use App\BaseModel;

class ActivityDate extends BaseModel
{
    protected $table = 'bravo_activity_dates';
    protected $casts = [
        'person_types'=>'array'
    ];

    public static function getDatesInRanges($start_date,$end_date){
        return static::query()->where([
            ['start_date','>=',$start_date],
            ['end_date','<=',$end_date],
        ])->take(100)->get();
    }
}