<?php
namespace Modules\Guide\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Modules\Booking\Models\Bookable;
use Modules\Booking\Models\Booking;
use Modules\Core\Models\Attributes;
use Modules\Core\Models\SEO;
use Modules\Core\Models\Terms;
use Modules\Media\Helpers\FileHelper;
use Modules\Review\Models\Review;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guide\Models\GuideTranslation;
use Modules\Guide\Models\GuideBooking;
use Modules\User\Models\UserWishList;

class Guide extends Bookable
{
    use SoftDeletes;
    protected $table                              = 'bravo_guides';
    public    $type                               = 'guide';
    public    $checkout_booking_detail_file       = 'Guide::frontend/booking/detail';
    public    $checkout_booking_detail_modal_file = 'Guide::frontend/booking/detail-modal';
    public    $email_new_booking_file             = 'Guide::emails.new_booking_detail';
    protected $fillable      = [
        'title',
        'content',
        'status',
    ];
    protected $slugField     = 'slug';
    protected $slugFromField = 'title';
    protected $seo_type      = 'guide';
    protected $casts = [
        'policy' => 'array',
    ];
    protected $bookingClass;
    protected $guideBookingClass;
    protected $reviewClass;
    protected $guideDateClass;
    protected $guideTermClass;
    protected $guideTranslationClass;
    protected $userWishListClass;
    protected $termClass;
    protected $attributeClass;
    protected $tmp_price = 0;
    protected $tmp_dates = [];
    protected $tmp_days = 0;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->bookingClass = Booking::class;
        $this->guideBookingClass = GuideBooking::class;
        $this->reviewClass = Review::class;
        $this->guideTermClass = GuideTerm::class;
        $this->guideTranslationClass = GuideTranslation::class;
        $this->userWishListClass = UserWishList::class;
        $this->termClass = Terms::class;
        $this->attributeClass = Attributes::class;
        $this->guideDateClass = GuideDate::class;
    }

    public static function getModelName()
    {
        return __("Guide");
    }

    public static function getTableName()
    {
        return with(new static)->table;
    }

    /**
     * Get SEO fop page list
     *
     * @return mixed
     */
    static public function getSeoMetaForPageList()
    {
        $meta['seo_title'] = __("Search for Spaces");
        if (!empty($title = setting_item_with_lang("guide_page_list_seo_title", false))) {
            $meta['seo_title'] = $title;
        } else if (!empty($title = setting_item_with_lang("guide_page_search_title"))) {
            $meta['seo_title'] = $title;
        }
        $meta['seo_image'] = null;
        if (!empty($title = setting_item("guide_page_list_seo_image"))) {
            $meta['seo_image'] = $title;
        } else if (!empty($title = setting_item("guide_page_search_banner"))) {
            $meta['seo_image'] = $title;
        }
        $meta['seo_desc'] = setting_item_with_lang("guide_page_list_seo_desc");
        $meta['seo_share'] = setting_item_with_lang("guide_page_list_seo_share");
        $meta['full_url'] = url(config('guide.guide_route_prefix'));
        return $meta;
    }

    public function terms()
    {
        return $this->hasMany($this->guideTermClass, "target_id");
    }

    public function termsByAttributeInListingPage()
    {
        $attribute = setting_item("guide_attribute_show_in_listing_page", 0);
        return $this->hasManyThrough($this->termClass, $this->guideTermClass, 'target_id', 'id', 'id', 'term_id')->where('bravo_terms.attr_id', $attribute)->with(['translations']);
    }

    public function getAttributeInListingPage()
    {
        $attribute_id = setting_item("guide_attribute_show_in_listing_page", 0);
        $attribute = $this->attributeClass::find($attribute_id);
        return $attribute ?? false;
    }

    public function getDetailUrl($include_param = true)
    {
        $param = [];
        if ($include_param) {
            if (!empty($date = request()->input('date'))) {
                $dates = explode(" - ", $date);
                if (!empty($dates)) {
                    $param['start'] = $dates[0] ?? "";
                    $param['end'] = $dates[1] ?? "";
                }
            }
            
        }
        $urlDetail = app_get_locale(false, false, '/') . config('guide.guide_route_prefix') . "/" . $this->slug;
        if (!empty($param)) {
            $urlDetail .= "?" . http_build_query($param);
        }
        return url($urlDetail);
    }

    public static function getLinkForPageSearch($locale = false, $param = [])
    {

        return url(app_get_locale(false, false, '/') . config('guide.guide_route_prefix') . "?" . http_build_query($param));
    }

    public function getGallery($featuredIncluded = false)
    {
        if (empty($this->gallery))
            return $this->gallery;
        $list_item = [];
        if ($featuredIncluded and $this->image_id) {
            $list_item[] = [
                'large' => FileHelper::url($this->image_id, 'full'),
                'thumb' => FileHelper::url($this->image_id, 'thumb')
            ];
        }
        $items = explode(",", $this->gallery);
        foreach ($items as $k => $item) {
            $large = FileHelper::url($item, 'full');
            $thumb = FileHelper::url($item, 'thumb');
            $list_item[] = [
                'large' => $large,
                'thumb' => $thumb
            ];
        }
        return $list_item;
    }

    public function getEditUrl()
    {
        return url(route('guide.admin.edit', ['id' => $this->id]));
    }

    public function getDiscountPercentAttribute()
    {
        if (!empty($this->price) and $this->price > 0 and !empty($this->sale_price) and $this->sale_price > 0 and $this->price > $this->sale_price) {
            $percent = 100 - ceil($this->sale_price / ($this->price / 100));
            return $percent . "%";
        }
    }

    public function fill(array $attributes)
    {
        if (!empty($attributes)) {
            foreach ($this->fillable as $item) {
                $attributes[$item] = $attributes[$item] ?? null;
            }
        }
        return parent::fill($attributes); // TODO: Change the autogenerated stub
    }

    public function isBookable()
    {
        if ($this->status != 'publish')
            return false;
        return parent::isBookable();
    }

    public function addToCart(Request $request)
    {
        $this->addToCartValidate($request);
        // Add Booking
        
        $discount = 0;
        $start_date = new \DateTime($request->input('start_date'));
        $end_date = new \DateTime($request->input('end_date'));
        $total = 0;
        
        //Buyer Fees
        $total_before_fees = $total;
        $list_fees = setting_item('guide_booking_buyer_fees');
        if (!empty($list_fees)) {
            $lists = json_decode($list_fees, true);
            foreach ($lists as $item) {
                if (!empty($item['per_person']) and $item['per_person'] == "on") {
                    //$total += $item['price'] * $total_guests;
                } else {
                    $total += $item['price'];
                }
            }
        }
        $booking = new $this->bookingClass();
        $booking->status = 'draft';
        $booking->object_id = $request->input('service_id');
        $booking->object_model = $request->input('service_type');
        $booking->vendor_id = $this->create_user;
        $booking->customer_id = Auth::id();
        $booking->total = $total;
        $booking->start_date = $start_date->format('Y-m-d H:i:s');
        $booking->end_date = $end_date->format('Y-m-d H:i:s');
        $booking->buyer_fees = $list_fees ?? '';
        $booking->total_before_fees = $total_before_fees;
        $booking->calculateCommission();
        $check = $booking->save();
        if ($check) {

            $this->bookingClass::clearDraftBookings();
            $booking->addMeta('duration', $this->duration);
            $booking->addMeta('base_price', $this->price);
            $booking->addMeta('sale_price', $this->sale_price);
            
            // Add Guide Booking
            
            $guideBooking = new $this->guideBookingClass();
            $guideBooking->fillByAttr([
                'room_id',
                'parent_id',
                'start_date',
                'end_date',
                'number',
                'booking_id',
                'price'
            ], [
                'room_id'    => null,
                'parent_id'  => $this->id,
                'start_date' => $start_date->format('Y-m-d H:i:s'),
                'end_date'   => $end_date->format('Y-m-d H:i:s'),
                'number'     => 1,
                'booking_id' => $booking->id,
                'price'      => $request->input('total_price')
            ]);
            $guideBooking->save();
                   
                
            
            $this->sendSuccess([
                'url' => $booking->getCheckoutUrl()
            ]);
        }
        $this->sendError(__("Can not check availability"));
    }

    public function addToCartValidate(Request $request)
    {
        $rules = [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d'
        ];
        // Validation
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $this->sendError('', ['errors' => $validator->errors()]);
            }
        }
        
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        if (strtotime($start_date) < strtotime(date('Y-m-d 00:00:00')) or strtotime($end_date) - strtotime($start_date) < DAY_IN_SECONDS) {
            $this->sendError(__("Your selected dates are not valid"));
        }
        if (!$this->checkBusyDate($start_date, $end_date)) {
            $this->sendError(__("Your selected dates are not valid"));
        }
        
        return true;
    }

    public function isAvailableInRanges($start_date, $end_date)
    {

        $days = max(1, floor((strtotime($end_date) - strtotime($start_date)) / DAY_IN_SECONDS));
        if ($this->default_state) {
            $notAvailableDates = $this->guideDateClass::query()->where([
                [
                    'start_date',
                    '>=',
                    $start_date
                ],
                [
                    'end_date',
                    '<=',
                    $end_date
                ],
                [
                    'active',
                    '0'
                ]
            ])->count('id');
            if ($notAvailableDates)
                return false;
        } else {
            $availableDates = $this->guideDateClass::query()->where([
                [
                    'start_date',
                    '>=',
                    $start_date
                ],
                [
                    'end_date',
                    '<=',
                    $end_date
                ],
                [
                    'active',
                    '=',
                    1
                ]
            ])->count('id');
            if ($availableDates <= $days)
                return false;
        }
        // Check Order
        $bookingInRanges = $this->bookingClass::getAcceptedBookingQuery($this->id, $this->type)->where([
            [
                'end_date',
                '>=',
                $start_date
            ],
            [
                'start_date',
                '<=',
                $end_date
            ],
        ])->count('id');
        if ($bookingInRanges) {
            return false;
        }
        return true;
    }

    public function getBookingData()
    {

        if (!empty($start = request()->input('start'))) {
            $start_html = display_date($start);
            $end_html = request()->input('end') ? display_date(request()->input('end')) : "";
            $date_html = $start_html . '<i class="fa fa-long-arrow-right" style="font-size: inherit"></i>' . $end_html;
        }
        $booking_data = [
            'id'              => $this->id,
            'person_types'    => [],
            'max'             => 0,
            'open_hours'      => [],
            'extra_price'     => [],
            'minDate'         => date('m/d/Y'),
            'buyer_fees'      => [],
            'start_date'      => request()->input('start') ?? "",
            'start_date_html' => $date_html ?? __('Please select'),
            'end_date'        => request()->input('end') ?? "",
        ];
        
        if ($this->enable_extra_price) {
            $booking_data['extra_price'] = $this->extra_price;
            if (!empty($booking_data['extra_price'])) {
                foreach ($booking_data['extra_price'] as $k => &$type) {
                    if (!empty($lang) and !empty($type['name_' . $lang])) {
                        $type['name'] = $type['name_' . $lang];
                    }
                    $type['number'] = 0;
                    $type['enable'] = 0;
                    $type['price_html'] = format_money($type['price']);
                    $type['price_type'] = '';
                    switch ($type['type']) {
                        case "per_day":
                            $type['price_type'] .= '/' . __('day');
                            break;
                        case "per_hour":
                            $type['price_type'] .= '/' . __('hour');
                            break;
                    }
                    
                }
            }
            $booking_data['extra_price'] = array_values((array)$booking_data['extra_price']);
        }
        $list_fees = setting_item_array('guide_booking_buyer_fees');
        if (!empty($list_fees)) {
            foreach ($list_fees as $item) {
                $item['type_name'] = $item['name_' . app()->getLocale()] ?? $item['name'] ?? '';
                $item['type_desc'] = $item['desc_' . app()->getLocale()] ?? $item['desc'] ?? '';
                $item['price_type'] = '';
                if (!empty($item['per_person']) and $item['per_person'] == 'on') {
                    $item['price_type'] .= '/' . __('guest');
                }
                $booking_data['buyer_fees'][] = $item;
            }
        }
        return $booking_data;
    }

    public static function searchForMenu($q = false)
    {
        $query = static::select('id', 'title as name');
        if (strlen($q)) {

            $query->where('title', 'like', "%" . $q . "%");
        }
        $a = $query->limit(10)->get();
        return $a;
    }

    public static function getMinMaxPrice()
    {
        $model = parent::selectRaw('MIN( price ) AS min_price ,
                                    MAX( price ) AS max_price ')->where("status", "publish")->first();
        if (empty($model->min_price) and empty($model->max_price)) {
            return [
                0,
                100
            ];
        }
        return [
            $model->min_price,
            $model->max_price
        ];
    }

    public function getReviewEnable()
    {
        return setting_item("guide_enable_review", 0);
    }

    public function getReviewApproved()
    {
        return setting_item("guide_review_approved", 0);
    }

    public function check_enable_review_after_booking()
    {
        $option = setting_item("guide_enable_review_after_booking", 0);
        if ($option) {
            $number_review = $this->reviewClass::countReviewByServiceID($this->id, Auth::id()) ?? 0;
            $number_booking = $this->bookingClass::countBookingByServiceID($this->id, Auth::id()) ?? 0;
            if ($number_review >= $number_booking) {
                return false;
            }
        }
        return true;
    }

    public function check_allow_review_after_making_completed_booking()
    {
        $options = setting_item("guide_allow_review_after_making_completed_booking", false);
        if (!empty($options)) {
            $status = json_decode($options);
            $booking = $this->bookingClass::select("status")->where("object_id", $this->id)->where("object_model", $this->type)->where("customer_id", Auth::id())->orderBy("id", "desc")->first();
            $booking_status = $booking->status ?? false;
            if (!in_array($booking_status, $status)) {
                return false;
            }
        }
        return true;
    }

    public static function getReviewStats()
    {
        $reviewStats = [];
        if (!empty($list = setting_item("guide_review_stats", []))) {
            $list = json_decode($list, true);
            foreach ($list as $item) {
                $reviewStats[] = $item['title'];
            }
        }
        return $reviewStats;
    }

    public function getReviewDataAttribute()
    {
        $list_score = [
            'score_total'  => 0,
            'score_text'   => __("Not rated"),
            'total_review' => 0,
            'rate_score'   => [],
        ];
        $dataTotalReview = $this->reviewClass::selectRaw(" AVG(rate_number) as score_total , COUNT(id) as total_review ")->where('object_id', $this->id)->where('object_model', $this->type)->where("status", "approved")->first();
        if (!empty($dataTotalReview->score_total)) {
            $list_score['score_total'] = number_format($dataTotalReview->score_total, 1);
            $list_score['score_text'] = Review::getDisplayTextScoreByLever(round($list_score['score_total']));
        }
        if (!empty($dataTotalReview->total_review)) {
            $list_score['total_review'] = $dataTotalReview->total_review;
        }
        $list_data_rate = $this->reviewClass::selectRaw('COUNT( CASE WHEN rate_number = 5 THEN rate_number ELSE NULL END ) AS rate_5,
                                                            COUNT( CASE WHEN rate_number = 4 THEN rate_number ELSE NULL END ) AS rate_4,
                                                            COUNT( CASE WHEN rate_number = 3 THEN rate_number ELSE NULL END ) AS rate_3,
                                                            COUNT( CASE WHEN rate_number = 2 THEN rate_number ELSE NULL END ) AS rate_2,
                                                            COUNT( CASE WHEN rate_number = 1 THEN rate_number ELSE NULL END ) AS rate_1 ')->where('object_id', $this->id)->where('object_model', $this->type)->where("status", "approved")->first()->toArray();
        for ($rate = 5; $rate >= 1; $rate--) {
            if (!empty($number = $list_data_rate['rate_' . $rate])) {
                $percent = ($number / $list_score['total_review']) * 100;
            } else {
                $percent = 0;
            }
            $list_score['rate_score'][$rate] = [
                'title'   => $this->reviewClass::getDisplayTextScoreByLever($rate),
                'total'   => $number,
                'percent' => round($percent),
            ];
        }
        return $list_score;
    }

    /**
     * Get Score Review
     *
     * Using for loop guide
     */
    public function getScoreReview()
    {
        $guide_id = $this->id;
        $list_score = Cache::rememberForever('review_' . $this->type . '_' . $guide_id, function () use ($guide_id) {
            $dataReview = $this->reviewClass::selectRaw(" AVG(rate_number) as score_total , COUNT(id) as total_review ")->where('object_id', $guide_id)->where('object_model', "guide")->where("status", "approved")->first();
            $score_total = !empty($dataReview->score_total) ? number_format($dataReview->score_total, 1) : 0;
            return [
                'score_total'  => $score_total,
                'total_review' => !empty($dataReview->total_review) ? $dataReview->total_review : 0,
            ];
        });
        $list_score['review_text'] = $list_score['score_total'] ? Review::getDisplayTextScoreByLever(round($list_score['score_total'])) : __("Not rated");
        return $list_score;
    }

    public function getNumberReviewsInService($status = false)
    {
        return $this->reviewClass::countReviewByServiceID($this->id, false, $status, $this->type) ?? 0;
    }

    public function getNumberServiceInLocation($location)
    {
        $number = 0;
        if (!empty($location)) {
            $number = parent::join('bravo_locations', function ($join) use ($location) {
                $join->on('bravo_locations.id', '=', $this->table . '.location_id')->where('bravo_locations._lft', '>=', $location->_lft)->where('bravo_locations._rgt', '<=', $location->_rgt);
            })->where($this->table . ".status", "publish")->with(['translations'])->count($this->table . ".id");
        }
        if (empty($number))
            return false;
        if ($number > 1) {
            return __(":number Guides", ['number' => $number]);
        }
        return __(":number Guide", ['number' => $number]);
    }

    /**
     * @param $from
     * @param $to
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getBookingsInRange($from, $to)
    {

        $query = $this->bookingClass::query();
        $query->whereNotIn('status', ['draft']);
        $query->where('start_date', '<=', $to)->where('end_date', '>=', $from)->take(50);
        $query->where('object_id', $this->id);
        $query->where('object_model', $this->type);
        return $query->orderBy('id', 'asc')->get();
    }

    public function saveCloneByID($clone_id)
    {
        $old = parent::find($clone_id);
        if (empty($old))
            return false;
        $selected_terms = $old->terms->pluck('term_id');
        $old->title = $old->title . " - Copy";
        $new = $old->replicate();
        $new->save();
        //Terms
        foreach ($selected_terms as $term_id) {
            $this->guideTermClass::firstOrCreate([
                'term_id'   => $term_id,
                'target_id' => $new->id
            ]);
        }
        //Language
        $langs = $this->guideTranslationClass::where("origin_id", $old->id)->get();
        if (!empty($langs)) {
            foreach ($langs as $lang) {
                $langNew = $lang->replicate();
                $langNew->origin_id = $new->id;
                $langNew->save();
                $langSeo = SEO::where('object_id', $lang->id)->where('object_model', $lang->getSeoType() . "_" . $lang->locale)->first();
                if (!empty($langSeo)) {
                    $langSeoNew = $langSeo->replicate();
                    $langSeoNew->object_id = $langNew->id;
                    $langSeoNew->save();
                }
            }
        }
        //SEO
        $metaSeo = SEO::where('object_id', $old->id)->where('object_model', $this->seo_type)->first();
        if (!empty($metaSeo)) {
            $metaSeoNew = $metaSeo->replicate();
            $metaSeoNew->object_id = $new->id;
            $metaSeoNew->save();
        }
    }

    public function hasWishList()
    {
        return $this->hasOne($this->userWishListClass, 'object_id', 'id')->where('object_model', $this->type)->where('user_id', Auth::id() ?? 0);
    }

    public function isWishList()
    {
        if (Auth::id()) {
            if (!empty($this->hasWishList) and !empty($this->hasWishList->id)) {
                return 'active';
            }
        }
        return '';
    }

    public static function getServiceIconFeatured()
    {
        return "fa fa-map-signs";
    }


    public static function isEnable()
    {
        return setting_item('guide_disable') == false;
    }
    public function getGuideAvailability($filters = [])
    {
        $guide = $this;
        $res = [];
        if ($guide->isAvailableAt($filters)) {
                $translation = $guide->translateOrOrigin(app()->getLocale());
                $res[] = [
                    'id'              => $guide->id,
                    'title'           => $translation->title,
                    'price'           => $guide->tmp_price ?? 0,                    
                    'days'            => $guide->tmp_days ?? 0,
                    'price_html'      => format_money($guide->tmp_price) . '<span class="unit">/' . ($guide->tmp_days ? __(':count days', ['count' => $guide->tmp_days]) : __(":count day", ['count' => $guide->tmp_days])) . '</span>'
                ];
            }
        return $res;
    }
    public function isAvailableAt($filters = []){

        if(empty($filters['start_date']) or empty($filters['end_date'])) return true;

        $guideDates =  $this->getDatesInRange($filters['start_date'],$filters['end_date']);
        $allDates = [];
        $tmp_price = 0;
        $tmp_day = 0;
        for($i = strtotime($filters['start_date']); $i < strtotime($filters['end_date']); $i+= DAY_IN_SECONDS)
        {
            $allDates[date('Y-m-d',$i)] = [
                'price'=>$this->price
            ];
            $tmp_day++;
        }

        if(!empty($guideDates))
        {
            foreach ($guideDates as $row)
            {
                if(!$row->active or !$row->price) return false;

                if(!array_key_exists(date('Y-m-d',strtotime($row->start_date)),$allDates)) continue;

                $allDates[date('Y-m-d',strtotime($row->start_date))] = [
                    'price'=>$row->price
                ];
            }
        }

        $guideBookings = $this->getBookingsInRange($filters['start_date'],$filters['end_date']);
        if(!empty($guideBookings)){
            foreach ($guideBookings as $guideBooking){
                for($i = strtotime($guideBooking->start_date); $i <= strtotime($guideBooking->end_date); $i+= DAY_IN_SECONDS)
                {
                    if(!array_key_exists(date('Y-m-d',$i),$allDates)) continue;
                    $allDates[date('Y-m-d',$i)]['number'] -= $guideBooking->number;

                    if($allDates[date('Y-m-d',$i)]['number'] <= 0){
                        return false;
                    }
                }
            }
        }

        


        $this->tmp_price = array_sum(array_column($allDates,'price'));
        $this->tmp_dates = $allDates;
        $this->tmp_days = $tmp_day;

        return true;
    }
    public function getDatesInRange($start_date,$end_date)
    {
        $query = $this->guideDateClass::query();
        $query->where('target_id',$this->id);
        $query->where('start_date','>=',date('Y-m-d H:i:s',strtotime($start_date)));
        $query->where('end_date','<=',date('Y-m-d H:i:s',strtotime($end_date)));
        return $query->take(40)->get();
    }
}
