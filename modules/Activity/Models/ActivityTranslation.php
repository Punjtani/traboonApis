<?php

namespace Modules\Activity\Models;

use App\BaseModel;

class ActivityTranslation extends Activity
{
    protected $table = 'bravo_activity_translations';

    protected $fillable = [
        'title',
        'content',
        'address',
        'policy'
    ];

    protected $slugField     = false;
    protected $seo_type = 'activity_translation';

    protected $cleanFields = [
        'content'
    ];
    protected $casts = [
        'policy'  => 'array',
    ];

    public function getSeoType(){
        return $this->seo_type;
    }
}