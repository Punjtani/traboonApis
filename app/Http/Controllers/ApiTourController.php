<?php
namespace App\Http\Controllers;
use Modules\Tour\Models\Tour;
use Illuminate\Http\Request;


class ApiTourController extends Controller
{
    public function __construct()
    {
        $this->tourClass = Tour::class;
        $this->locationClass = Location::class;
    }
    public function index()
    {
        $model_car = $this->tourClass::select("bravo_tours.*")->get();
        return response($model_car);
    }
    public function store(Request $request)
    {
        $model_car=$this->tourClass::create($request->all());
        return response($model_car);
    }
    public function edit(Car $car)
    {
        $model_car = $this->tourClass::select("bravo_tours.*")->where('id',$car->id)->get();
        return response($model_car);
    }
    public function destroy(Car $car)
    {
        $model_car = $this->tourClass::select("bravo_tours.*")->where('id',$car->id)->delete();
        return $model_car;
    }
    public function update(Request $request,Car $car)
    {
        $model_car = $this->tourClass::select("bravo_tours.*")->where('id',$car->id)->update(
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
}

