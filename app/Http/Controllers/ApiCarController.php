<?php
namespace App\Http\Controllers;
use DB;
use Modules\Car\Models\Car;
use Illuminate\Http\Request;
use Modules\Location\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

class ApiCarController extends Controller
{
    public function __construct()
    {
        $this->carClass = Car::class;
        $this->locationClass = Location::class;
    }
    public function index()
    {
        $model_car = $this->carClass::select("bravo_cars.*")->get();
        return response($model_car);
    }
    public function store(Request $request)
    {
        $model_car=$this->carClass::create($request->all());
        return response($model_car);
    }
    public function edit(Car $car)
    {
        $model_car = $this->carClass::select("bravo_cars.*")->where('id',$car->id)->get();
        return response($model_car);
    }
    public function destroy(Car $car)
    {
        $model_car = $this->carClass::select("bravo_cars.*")->where('id',$car->id)->delete();
        return $model_car;
    }
    public function update(Request $request,Car $car)
    {
        $model_car = $this->carClass::select("bravo_cars.*")->where('id',$car->id)->update(
        [
        "title"=>$request->title,
        "slug"=> $request->slug,
        "content"=> $request->content,
        "image_id"=> $request->image_id,
        "banner_image_id"=> $request->banner_image_id,
        "location_id"=>$request->location_id,
        "address"=>$request->address,
         "map_lat"=> $request->map_lat,
         "map_lng"=> $request->map_lng,
         "map_zoom"=> $request->map_zoom,
         "is_featured"=> $request->is_featured,
         "gallery"=> $request->gallery,
         "video"=> $request->video,
         "faqs"=> $request->faqs,
         "number"=> $request->number,
         "price"=> $request->price,
         "sale_price"=> $request->sale_price,
         "is_instant"=> $request->is_instant,
         "enable_extra_price"=> $request->enable_extra_price,
         "extra_price"=> $request->extra_price,
         "discount_by_days"=> $request->discount_by_days,
         "passenger"=> $request->passenger,
         "gear"=> $request->gear,
         "baggage"=> $request->baggage,
         "door"=> $request->door,
         "status"=> $request->status,
         "default_state"=> $request->default_state,
         "create_user"=> $request->create_user,
         "update_user"=> $request->update_user,
        ]);

        return $model_car;
    }
    public function search(Request $request)
    {

        $model_car = $this->carClass::select("bravo_cars.*")->get();
        $arr=[];
            if (!empty($location_id = $request->location_id)) {
                $location = $this->locationClass::where('id', $location_id)->where("status","publish")->first();
                if(!empty($location)){

                   $v= Car::join('bravo_locations','bravo_cars.location_id','=','bravo_locations.id')
                    ->where('bravo_cars.location_id',$location->id)
                    ->select('bravo_cars.*')
                    ->get();
                    // $s=DB::table('bravo_car_dates')->get();

                    foreach ($v as $value) {
                       $er= DB::table('bravo_car_dates')->where([
                            ['target_id','=',$value->id],
                            ['start_date','=',$request->start],
                            ['end_date','=',$request->end],
                            ['active','=',1]])
                            ->value('target_id');
                            if($er)
                            {
                                $s=DB::table('bravo_cars')->where('id',$er)->get();
                                array_push($arr,$s);
                            }
                    }

                }
            }

             $v=new Collection($arr);
            return response()->json($v);
    }
}

