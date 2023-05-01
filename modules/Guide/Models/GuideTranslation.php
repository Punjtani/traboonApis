<?php

namespace Modules\Guide\Models;

use App\BaseModel;

class GuideTranslation extends Guide
{
    protected $table = 'bravo_guide_translations';

    protected $fillable = [
        'title',
        'content',
        'address',
        'policy'
    ];

    protected $slugField     = false;
    protected $seo_type = 'guide_translation';

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