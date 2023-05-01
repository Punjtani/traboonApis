<?php

    namespace Modules\Virtual\Models;

    use App\BaseModel;
    use Kalnoy\Nestedset\NodeTrait;
    use Modules\Booking\Models\Bookable;
    use Modules\Media\Helpers\FileHelper;
    use Illuminate\Database\Eloquent\SoftDeletes;
    use Modules\Core\Models\SEO;

    class Virtual extends Bookable
    {
        use SoftDeletes;
        use NodeTrait;
        protected $table         = 'bravo_virtuals';
        protected $fillable      = [
            'name',
            'video',
            'voice',
            'slug',
            'content',
            'image_id',
            'lat',
            'long',
            'status',
            'banner_image_id',
            'trip_ideas',
        ];
        protected $slugField     = 'slug';
        protected $slugFromField = 'name';
        protected $seo_type      = 'virtual';
        protected $casts         = [
            'trip_ideas' => 'array'
        ];

        public static function getModelName()
        {
            return __("Virtual");
        }

        public static function searchForMenu($q = false)
        {
            $query = static::select('id', 'name');
            if (strlen($q)) {

                $query->where('name', 'like', "%" . $q . "%");
            }
            $a = $query->limit(10)->get();
            return $a;
        }

        public function getImageUrl($size = "medium")
        {
            $url = FileHelper::url($this->image_id, $size);
            return $url ? $url : '';
        }

        public function getDisplayNumberServiceInVirtual($service_type)
        {
            $allServices = get_bookable_services();
            if(empty($allServices[$service_type])) return false;
            $module = new $allServices[$service_type];
            return $module->getNumberServiceInVirtual($this);
        }


        public function getDetailUrl($locale = false)
        {
            return url(app_get_locale(false, false, '/') . config('virtual.virtual_route_prefix') . "/" . $this->slug);
        }

        public function getLinkForPageSearch($service_type)
        {
            $allServices = get_bookable_services();
            $module = new $allServices[$service_type];
            return $module->getLinkForPageSearch(false, ['virtual_id' => $this->id]);
        }
    }
