<?php
namespace Modules\Guide\Models;

use App\BaseModel;

class GuideTerm extends BaseModel
{
    protected $table = 'bravo_guide_term';
    protected $fillable = [
        'term_id',
        'target_id'
    ];
}