<?php
namespace Modules\Virtual\Models;

use App\BaseModel;

class VirtualTranslation extends BaseModel
{
    protected $table = 'bravo_virtual_translations';
    protected $fillable = ['name', 'content','trip_ideas'];
    protected $seo_type = 'virtual_translation';
    protected $cleanFields = [
        'content'
    ];
    protected $casts = [
        'trip_ideas'  => 'array',
    ];
}
