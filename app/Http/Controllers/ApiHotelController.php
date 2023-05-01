<?php
namespace App\Http\Controllers;


use Modules\Hotel\Models\Hotel;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Modules\Location\Models\Location;
use DB;
use Illuminate\Database\Eloquent\Collection;

class ApiHotelController extends Controller
{
    public function __construct()
    {
        $this->hotelClass = Hotel::class;
        $this->locationClass = Location::class;
    }
    public function index()
    {
        $model_hotel = $this->hotelClass::select("bravo_hotels.*")->get();
        return response($model_hotel);
    }
    public function store(Request $request)
    {
        $model_hotel=$this->hotelClass::create($request->all());
        return response($model_hotel);
    }
    public function edit(Hotel $hotel)
    {
        $model_hotel = $this->hotelClass::select("bravo_hotels.*")->where('id',$hotel->id)->get();
        return response($model_hotel);
    }
    public function destroy(Hotel $hotel)
    {

        $model_hotel = $this->hotelClass::select("bravo_hotels.*")->where('id',$hotel->id)->delete();
        return $model_hotel;
    }
    public function update(Request $request,Hotel $hotel)
    {
        $model_hotel = $this->hotelClass::select("bravo_hotels.*")->where('id',$hotel->id)->update(
        [
        "title"=>$request->title,
        "slug"=> $request->slug,
        "content"=> $request->content,
        "image_id"=> $request->image_id,
        "banner_image_id"=> $request->banner_image_id,
        "location_id"=>$request->location_id,
        "address"=>null,
         "map_lat"=> null,
         "map_lng"=>null,
        "map_zoom"=> 8,
        "is_featured"=> null,
        "gallery"=> "195,194,193,192,191,190,",
        "video"=> "https://www.youtube.com/watch?v=T8f1fKsiLvE",
        "policy"=> null,
        "star_rate"=> 3,
        "price"=> "2000.00",
        "check_in_time"=> null,
        "check_out_time"=> null,
        "allow_full_day"=> null,
        "sale_price"=> null,
        "status"=> "publish",
        "create_user"=> 22,
        "update_user"=> null,
        "deleted_at"=> null,
        "review_score"=> null,
        "ical_import_url"=> null,
        "rank"=> null
        ]);

        return $model_hotel;
    }
    public function search(Request $request)
    {
        $model_hotel = $this->hotelClass::select("bravo_hotels.*")->get();

            if (!empty($location_id = $request->location_id)) {
                $location = $this->locationClass::where('id', $location_id)->where("status","publish")->first();

                if(!empty($location)){
                   $v= DB::table('bravo_hotels')->join('bravo_locations','bravo_hotels.location_id','=','bravo_locations.id')
                    ->where('bravo_hotels.location_id',$location->id)
                    ->select('bravo_hotels.*')
                    ->get();

                    // $s=DB::table('bravo_hotel_dates')->get();
                    $var=[];
                    foreach ($v as $value) {

                       $er= DB::table('bravo_hotel_room_dates')->where([
                            ['target_id','=',$value->id],
                            ['start_date','=',$request->start],
                            ['end_date','=',$request->end],
                            ['max_guests','=',$request->max_guests],
                            ['active','=',1]])
                            ->value('target_id');


                            if($er)
                            {
                                $s=DB::table('bravo_hotels')->where('id',$er)->get();
                                array_push($var,$s);

                            }
                    }

                }
            }

            // $v=new Collection($var);
            return response()->json($var);
    }
}

