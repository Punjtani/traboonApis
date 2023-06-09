<?php
namespace Modules\Activity\Controllers;

use App\Http\Controllers\Controller;
use Modules\Activity\Models\Activity;
use Modules\Hotel\Models\Hotel;
use Modules\Car\Models\Car;
use Modules\Guide\Models\Guide;
use Modules\Tour\Models\Tour;
use Illuminate\Http\Request;
use Modules\Location\Models\Location;
use Modules\Review\Models\Review;
use Modules\Core\Models\Attributes;
use Modules\Media\Helpers\FileHelper;
use DB;

class ActivityController extends Controller
{
    protected $activityClass;
    protected $locationClass;
    public function __construct()
    {
        $this->activityClass = Activity::class;
        $this->locationClass = Location::class;
    }
    public function callAction($method, $parameters)
    {
        if(!Activity::isEnable())
        {
            return redirect('/');
        }
        return parent::callAction($method, $parameters); // TODO: Change the autogenerated stub
    }

    public function index(Request $request)
    {
        
        $is_ajax = $request->query('_ajax');
        
        $model_activity = $this->activityClass::select("bravo_activitys.*");
        $model_activity->where("bravo_activitys.status", "publish");
        if (!empty($location_id = $request->query('location_id'))) {
            $location = $this->locationClass::where('id', $location_id)->where("status","publish")->first();
            if(!empty($location)){
                $model_activity->join('bravo_locations', function ($join) use ($location) {
                    $join->on('bravo_locations.id', '=', 'bravo_activitys.location_id')
                        ->where('bravo_locations._lft', '>=', $location->_lft)
                        ->where('bravo_locations._rgt', '<=', $location->_rgt);
                });
            }
        }
        if (!empty($price_range = $request->query('price_range'))) {
            $pri_from = explode(";", $price_range)[0];
            $pri_to = explode(";", $price_range)[1];
            $raw_sql_min_max = "(  bravo_activitys.price >= ? ) 
                            AND (  bravo_activitys.price <= ? )";
            $model_activity->WhereRaw($raw_sql_min_max,[$pri_from,$pri_to]);
        }
        if (!empty($min_age = $request->query('min_age')) 
                && 
                !empty($max_age = $request->query('max_age'))) {
            $min_year = $min_age;
            $max_year =  $max_age;
            
            $raw_sql_min_max = "(  bravo_activitys.duration >= ? ) 
                            AND (  bravo_activitys.duration <= ? )";
            $model_activity->WhereRaw($raw_sql_min_max,[$min_year,$max_year]);
            
        }
        if (!empty($star_rate = $request->query('star_rate'))) {
            $model_activity->WhereIn('star_rate',$star_rate);
        }

        $terms = $request->query('terms');
        if($term_id = $request->query('term_id'))
        {
            $terms[] = $term_id;
        }
        if (is_array($terms) && !empty($terms)) {
            $model_activity->join('bravo_activity_term as tt', 'tt.target_id', "bravo_activitys.id")->whereIn('tt.term_id', $terms);
        }

        $review_scores = $request->query('review_score');
        if (is_array($review_scores) && !empty($review_scores)) {
            $where_review_score = [];
            foreach ($review_scores as $number){
                $where_review_score[] = " ( bravo_activitys.review_score >= {$number} AND bravo_activitys.review_score <= {$number}.9 ) ";
            }
            $sql_where_review_score = " ( " . implode("OR", $where_review_score) . " )  ";
            $model_activity->WhereRaw($sql_where_review_score);
        }

        $model_activity->orderBy("id", "desc");
        $model_activity->groupBy("bravo_activitys.id");

        $list = $model_activity->with(['location','hasWishList','translations','termsByAttributeInListingPage'])->paginate(20);
        $markers = [];
        if (!empty($list)) {
            foreach ($list as $row) {
                $markers[] = [
                    "id"      => $row->id,
                    "title"   => $row->title,                    
                    "lat"     => (float)$row->map_lat,
                    "lng"     => (float)$row->map_lng,
                    "gallery" => $row->getGallery(true),
                    "infobox" => view('Activity::frontend.layouts.search.loop-grid', ['row' => $row,'disable_lazyload'=>1,'wrap_class'=>'infobox-item'])->render(),
                    'marker'  => url('images/icons/png/pin.png'),
                ];
            }
        }
        $limit_location = 15;
        if( empty(setting_item("activity_location_search_style")) or setting_item("activity_location_search_style") == "normal" ){
            $limit_location = 1000;
        }
        
        $data = [
            'rows'               => $list,
            'list_location'      => $this->locationClass::where('status', 'publish')->limit($limit_location)->with(['translations'])->get()->toTree(),
            'activity_min_max_price' => $this->activityClass::getMinMaxPrice(),
            'markers'            => $markers,
            "blank"              => 1,
            "seo_meta"           => $this->activityClass::getSeoMetaForPageList()
        ];
        $layout = setting_item("activity_layout_search", 'normal');
        if ($request->query('_layout')) {
            $layout = $request->query('_layout');
        }
        if ($is_ajax) {
            $this->sendSuccess([
                'html'    => view('Activity::frontend.layouts.search-map.list-item', $data)->render(),
                "markers" => $data['markers']
            ]);
        }
        $data['attributes'] = Attributes::where('service', 'activity')->with(['terms','translations'])->get();

        if ($layout == "map") {
            $data['body_class'] = 'has-search-map';
            $data['html_class'] = 'full-page';
            return view('Activity::frontend.search-map', $data);
        }        
        return view('Activity::frontend.search', $data);
    }
    
    public function detail(Request $request, $slug)
    {
        $row = $this->activityClass::where('slug', $slug)->where("status", "publish")->with(['location','translations','hasWishList'])->first();;
        if (empty($row)) {
            return redirect('/');
        }
        $translation = $row->translateOrOrigin(app()->getLocale());
        $activity_related = [];
        $location_id = $row->location_id;
        if (!empty($location_id)) {
            $activity_related = $this->activityClass::where('location_id', $location_id)->where("status", "publish")->take(4)->whereNotIn('id', [$row->id])->with(['location','translations','hasWishList'])->get();
        }
        $review_list = Review::where('object_id', $row->id)->where('object_model', 'activity')->where("status", "approved")->orderBy("id", "desc")->with('author')->paginate(setting_item('activity_review_number_per_page', 5));
        $data = [
            'row'          => $row,
            'translation'       => $translation,
            'activity_related' => $activity_related,
            'booking_data' => $row->getBookingData(),
            'review_list'  => $review_list,
            'seo_meta'  => $row->getSeoMetaWithTranslation(app()->getLocale(),$translation),
            'body_class'=>'is_single'
        ];
        $this->setActiveMenu($row);
        return view('Activity::frontend.detail', $data);
    }
    
    //Called when activities are selected
    //Proceed button is clicked
    public function selected(Request $request)
    {
        
        $selectedActivities = array();
        $relatedHotels = array();
        $relatedCars = array();
        $relatedGuides = array();
        $relatedTours = array();
        $totalCost = 0;
        $totalActivities = 0 ;
        $locationIds = array();
        foreach($request->activity_ids as $id)
        {            
            $activity = Activity::where('id', $id)->first();             
            $selectedActivities[] = [
                "id" => $activity->id,
                "title" => $activity->title,
                "activity_link" => url("/custom_trip/".$activity->slug),
                "activity_img_link" => $activity->getImageLink(),
                "price" => $activity->price
            ];
            if( !in_array ( $activity->location_id, $locationIds  ))
            {
                $locationIds[] = $activity->location_id;                
            }
               
            $totalCost += $activity->price;
            $totalActivities++;
            
        }
        foreach ($locationIds as $id) {
            //Related hotels
            $hotels = Hotel::where([['location_id','=',$id],['deleted_at','=',NULL],['status','=','publish']])
               ->orderBy('price', 'asc')
               ->orderBy('review_score', 'desc')
               ->orderBy('star_rate', 'desc')
               ->take(3)
               ->get();
            foreach($hotels as $hotel)
            {
                $relatedHotels[]=[
                    'id' => $hotel->id,
                    'title' => $hotel->title,
                    'hotel_link' => url('/hotel/'.$hotel->slug),
                    'hotel_img_link' => FileHelper::url($hotel->image_id, 'thumb'),
                    'price' => $hotel->price,
                    'location' => (Location::find($hotel->location_id))->name,
                    'star_rate' => $hotel->star_rate,
                    'review_score' => $hotel->review_score,
                ];
            }
            //Related cars
            $cars = Car::where([['location_id','=',$id],['deleted_at','=',NULL],['status','=','publish']])
               
               ->orderBy('price', 'asc')
               ->orderBy('review_score', 'desc')
               ->take(3)
               ->get();
            foreach($cars as $car)
            {
                $relatedCars[]=[
                    'id' => $car->id,
                    'title' => $car->title,
                    'car_link' => url('/car/'.$car->slug),
                    'car_img_link' => FileHelper::url($car->image_id, 'thumb'),
                    'price' => $car->price,
                    'location' => (Location::find($car->location_id))->name,
                    'review_score' => $car->review_score,
                ];
            }
            //Related guides
            $guides = Guide::where([['location_id','=',$id],['deleted_at','=',NULL],['status','=','publish']])
               
               ->orderBy('price', 'asc')
               ->orderBy('review_score', 'desc')
               ->orderBy('star_rate', 'desc')
               ->take(3)
               ->get();
            foreach($guides as $guide)
            {
                $relatedGuides[]=[
                    'id' => $guide->id,
                    'title' => $guide->title,
                    'guide_link' => url('/guide/'.$guide->slug),
                    'guide_img_link' => FileHelper::url($guide->image_id, 'thumb'),
                    'price' => $guide->price,
                    'location' => (Location::find($guide->location_id))->name,
                    'review_score' => $guide->review_score,
                ];
            }
            //Related tour
            $tours = Tour::where([['location_id','=',$id],['deleted_at','=',NULL],['status','=','publish']])
               
               ->orderBy('price', 'asc')
               ->orderBy('review_score', 'desc')
               ->take(3)
               ->get();
            foreach($tours as $tour)
            {
                $relatedTours[]=[
                    'id' => $tour->id,
                    'title' => $tour->title,
                    'tour_link' => url('/tour/'.$tour->slug),
                    'tour_img_link' => FileHelper::url($tour->image_id, 'thumb'),
                    'price' => $tour->price,
                    'location' => (Location::find($tour->location_id))->name,
                    'review_score' => $tour->review_score,
                ];
            }
        }
        
        $data['selectedActivities'] = $selectedActivities;        
        $data['relatedHotels'] = $relatedHotels;
        $data['relatedGuides'] = $relatedGuides;
        $data['relatedCars'] = $relatedCars;
        $data['relatedTours'] = $relatedTours;
        $data['totalCost'] = $totalCost;
        $data['totalActivities'] =  $totalActivities;
        return view('Activity::frontend.selected', $data);
    }
    public function checkAvailability(){
        $activity_id = \request('activity_id');

        if(!\request()->input('firstLoad')) {
            request()->validate([
                'activity_id'   => 'required',
                'start_date' => 'required:date_format:Y-m-d',
                'end_date'   => 'required:date_format:Y-m-d',
            ]);

            if(strtotime(\request('end_date')) - strtotime(\request('start_date')) < DAY_IN_SECONDS){
                $this->sendError(__("Dates are not valid"));
            }
            if(strtotime(\request('end_date')) - strtotime(\request('start_date')) > 30*DAY_IN_SECONDS){
                $this->sendError(__("Maximum day for booking is 30"));
            }
        }

        $activity = $this->activityClass::find($activity_id);
        if(empty($activity_id) or empty($activity)){
            $this->sendError(__("Activity not found"));
        }

        $rooms = $activity->getActivityAvailability(request()->input());

        $this->sendSuccess([
            'rooms'=>$rooms
        ]);
    }
}
