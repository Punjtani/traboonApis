<?php
namespace Modules\Booking\Controllers;

use DebugBar\DebugBar;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
//use Modules\Booking\Events\VendorLogPayment;
use Modules\Tour\Models\TourDate;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;
use Modules\Activity\Models\Activity;
use App\Helpers\ReCaptchaEngine;

class BookingController extends \App\Http\Controllers\Controller
{
    use AuthorizesRequests;
    protected $booking;

    public function __construct()
    {
        $this->booking = Booking::class;
    }

    public function checkout($code)
    {

        $booking = $this->booking::where('code', $code)->first();

        if (empty($booking)) {
            abort(404);
        }
        if ($booking->customer_id != Auth::id()) {
            abort(404);
        }

        if($booking->status != 'draft'){
            return redirect('/');
        }
        $data = [
            'page_title' => __('Checkout'),
            'booking'    => $booking,
            'service'    => $booking->service,
            'gateways'   => $this->getGateways(),
            'user'       => Auth::user()
        ];
        return view('Booking::frontend/checkout', $data);
    }
    
    //this function is called in case of activities
    public function checkoutMany($code)
    {

        $bookings = $this->booking::where('bulk_id', $code)->get();
        $bulk_id = "";
        $services = array();
        if (empty($bookings)) {
            abort(404);
        }
        foreach ($bookings as $booking)
        {
            if ($booking->customer_id != Auth::id()) {
                abort(404);
            }
            if($booking->status != 'draft'){
                return redirect('/');
            }
            //$services[] = $booking->service;
            $bulk_id = $booking->bulk_id;
        }
        

        
        $data = [
            'page_title' => __('Checkout'),
            'bookings'    => $bookings,
            'bulk_id' => $bulk_id,
            'gateways'   => $this->getGateways(),
            'user'       => Auth::user()
        ];
        return view('Booking::frontend/checkout-many', $data);
    }

    public function checkStatusCheckout($code)
    {
        $booking = $this->booking::where('code', $code)->first();
        $data = [
            'error'    => false,
            'message'  => '',
            'redirect' => ''
        ];
        if (empty($booking)) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        if ($booking->customer_id != Auth::id()) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        if ($booking->status != 'draft') {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        return response()->json($data, 200);
    }

    public function doCheckout(Request $request)
    {

        /**
         * @param Booking $booking
         */
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            $this->sendError('', ['errors' => $validator->errors()]);
        }
        $code = $request->input('code');
        $booking = $this->booking::where('code', $code)->first();
        if (empty($booking)) {
            abort(404);
        }
        if ($booking->customer_id != Auth::id()) {
            abort(404);
        }
        if ($booking->status != 'draft') {
            return $this->sendError('',[
                'url'=>$booking->getDetailUrl()
            ]);
        }
        $service = $booking->service;
        if (empty($service)) {
            $this->sendError(__("Service not found"));
        }
        /**
         * Google ReCapcha
         */
        if(ReCaptchaEngine::isEnable() and setting_item("booking_enable_recaptcha")){
            $codeCapcha = $request->input('g-recaptcha-response');
            if(!$codeCapcha or !ReCaptchaEngine::verify($codeCapcha)){
                $this->sendError(__("Please verify the captcha"));
            }
        }
        $rules = [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255',
            'phone'           => 'required|string|max:255',
            'country' => 'required',
            'payment_gateway' => 'required',
            'term_conditions' => 'required'
        ];
        $rules = $service->filterCheckoutValidate($request, $rules);
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $this->sendError('', ['errors' => $validator->errors()]);
            }
        }
        if (!empty($rules['payment_gateway'])) {
            $payment_gateway = $request->input('payment_gateway');
            $gateways = get_payment_gateways();
            if (empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway])) {
                $this->sendError(__("Payment gateway not found"));
            }
            $gatewayObj = new $gateways[$payment_gateway]($payment_gateway);
            if (!$gatewayObj->isAvailable()) {
                $this->sendError(__("Payment gateway is not available"));
            }
        }
        $service->beforeCheckout($request, $booking);
        // Normal Checkout
        $booking->first_name = $request->input('first_name');
        $booking->last_name = $request->input('last_name');
        $booking->email = $request->input('email');
        $booking->phone = $request->input('phone');
        $booking->address = $request->input('address_line_1');
        $booking->address2 = $request->input('address_line_2');
        $booking->city = $request->input('city');
        $booking->state = $request->input('state');
        $booking->zip_code = $request->input('zip_code');
        $booking->country = $request->input('country');
        $booking->customer_notes = $request->input('customer_notes');
        $booking->gateway = $payment_gateway;
        $booking->save();

//        event(new VendorLogPayment($booking));

        $user = Auth::user();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address_line_1');
        $user->address2 = $request->input('address_line_2');
        $user->city = $request->input('city');
        $user->state = $request->input('state');
        $user->zip_code = $request->input('zip_code');
        $user->country = $request->input('country');
        $user->save();

        $booking->addMeta('locale',app()->getLocale());

        $service->afterCheckout($request, $booking);
        try {

            $gatewayObj->process($request, $booking, $service);
        } catch (Exception $exception) {
            $this->sendError($exception->getMessage());
        }
    }
    
    //Perform bulk check out in case of activity    
    public function doCheckoutBulk(Request $request)
    {

        /**
         * @param Booking $booking
         */
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            $this->sendError('', ['errors' => $validator->errors()]);
        }
        $code = $request->input('code');
        $booking = $this->booking::where('code', $code)->first();
        if (empty($booking)) {
            abort(404);
        }
        if ($booking->customer_id != Auth::id()) {
            abort(404);
        }
        if ($booking->status != 'draft') {
            return $this->sendError('',[
                'url'=>$booking->getDetailUrl()
            ]);
        }
        $service = $booking->service;
        if (empty($service)) {
            $this->sendError(__("Service not found"));
        }
        /**
         * Google ReCapcha
         */
        if(ReCaptchaEngine::isEnable() and setting_item("booking_enable_recaptcha")){
            $codeCapcha = $request->input('g-recaptcha-response');
            if(!$codeCapcha or !ReCaptchaEngine::verify($codeCapcha)){
                $this->sendError(__("Please verify the captcha"));
            }
        }
        $rules = [
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|string|email|max:255',
            'phone'           => 'required|string|max:255',
            'country' => 'required',
            'payment_gateway' => 'required',
            'term_conditions' => 'required'
        ];
        $rules = $service->filterCheckoutValidate($request, $rules);
        if (!empty($rules)) {
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $this->sendError('', ['errors' => $validator->errors()]);
            }
        }
        if (!empty($rules['payment_gateway'])) {
            $payment_gateway = $request->input('payment_gateway');
            $gateways = get_payment_gateways();
            if (empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway])) {
                $this->sendError(__("Payment gateway not found"));
            }
            $gatewayObj = new $gateways[$payment_gateway]($payment_gateway);
            if (!$gatewayObj->isAvailable()) {
                $this->sendError(__("Payment gateway is not available"));
            }
        }
        $service->beforeCheckout($request, $booking);
        // Normal Checkout
        $booking->first_name = $request->input('first_name');
        $booking->last_name = $request->input('last_name');
        $booking->email = $request->input('email');
        $booking->phone = $request->input('phone');
        $booking->address = $request->input('address_line_1');
        $booking->address2 = $request->input('address_line_2');
        $booking->city = $request->input('city');
        $booking->state = $request->input('state');
        $booking->zip_code = $request->input('zip_code');
        $booking->country = $request->input('country');
        $booking->customer_notes = $request->input('customer_notes');
        $booking->gateway = $payment_gateway;
        $booking->save();

//        event(new VendorLogPayment($booking));

        $user = Auth::user();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        $user->address = $request->input('address_line_1');
        $user->address2 = $request->input('address_line_2');
        $user->city = $request->input('city');
        $user->state = $request->input('state');
        $user->zip_code = $request->input('zip_code');
        $user->country = $request->input('country');
        $user->save();

        $booking->addMeta('locale',app()->getLocale());

        $service->afterCheckout($request, $booking);
        try {

            $gatewayObj->process($request, $booking, $service);
        } catch (Exception $exception) {
            $this->sendError($exception->getMessage());
        }
    }
    public function confirmPayment(Request $request, $gateway)
    {

        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
            $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
            $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->confirmPayment($request);
    }

    public function cancelPayment(Request $request, $gateway)
    {

        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
            $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
            $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->cancelPayment($request);
    }

    /**
     * @todo Handle Add To Cart Validate
     *
     * @param Request $request
     * @return string json
     */
    public function addToCart(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'service_id'   => 'required|integer',
            'service_type' => 'required'
        ]);
        if ($validator->fails()) {
            $this->sendError('', ['errors' => $validator->errors()]);
        }
        $service_type = $request->input('service_type');
        $service_id = $request->input('service_id');
        $allServices = get_bookable_services();
        if (empty($allServices[$service_type])) {
            $this->sendError(__('Service type not found'));
        }
        $module = $allServices[$service_type];
        $service = $module::find($service_id);
        if (empty($service) or !is_subclass_of($service, '\\Modules\\Booking\\Models\\Bookable')) {
            $this->sendError(__('Service not found'));
        }
        if (!$service->isBookable()) {
            $this->sendError(__('Service is not bookable'));
        }
        //        try{
        $service->addToCart($request);
        //
        //        }catch(\Exception $ex){
        //            $this->sendError($ex->getMessage(),['code'=>$ex->getCode()]);
        //        }
    }
    
    /**
     * @todo Handle Add To Cart Validate
     *
     * @param Request $request
     * @return string json
     */
    public function addToCartMany(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'data'   => 'required|array',
        ]);
        if ($validator->fails()) {
            $this->sendError('', ['errors' => $validator->errors()]);
        }
                
        foreach($request->input('data') as $item)
        {
            $allServices = get_bookable_services();
            if (empty($allServices[$item['service_type']])) {
                $this->sendError(__('Service type not found'));
            }
            $module = $allServices[$item['service_type']];
            $service = $module::find($item['service_id']);
            if (empty($service) or !is_subclass_of($service, '\\Modules\\Booking\\Models\\Bookable')) {
                $this->sendError(__('Service not found'));
            }
            if (!$service->isBookable()) {
                $this->sendError(__('Service is not bookable'));
            }
        }
        
        //        try{
        $service = new Activity();
        $service->addToCart($request);
        //
        //        }catch(\Exception $ex){
        //            $this->sendError($ex->getMessage(),['code'=>$ex->getCode()]);
        //        }
    }
    
    protected function getGateways()
    {

        $all = get_payment_gateways();
        $res = [];
        foreach ($all as $k => $item) {
            if (class_exists($item)) {
                $obj = new $item($k);
                if ($obj->isAvailable()) {
                    $res[$k] = $obj;
                }
            }
        }
        
        return $res;
    }

    public function detail(Request $request, $code)
    {

        $booking = Booking::where('code', $code)->first();
        if (empty($booking)) {
            abort(404);
        }

        if ($booking->status == 'draft') {
            return redirect($booking->getCheckoutUrl());
        }
        if ($booking->customer_id != Auth::id()) {
            abort(404);
        }
        $data = [
            'page_title' => __('Booking Details'),
            'booking'    => $booking,
            'service'    => $booking->service,
        ];
        if ($booking->gateway) {
            $data['gateway'] = get_payment_gateway_obj($booking->gateway);
        }
        return view('Booking::frontend/detail', $data);
    }
    
    public function exportIcal($service_type = 'tour', $id)
    {
            \Debugbar::disable();
            $allServices = get_bookable_services();
            if (empty($allServices[$service_type])) {
                    $this->sendError(__('Service type not found'));
            }
            $module = $allServices[$service_type];

            $path ='/ical/';
            $fileName = 'booking_' . $service_type . '_' . $id . '.ics';
            $fullPath = $path.$fileName;

            $content  = $this->booking::getContentCalendarIcal($service_type,$id,$module);
            Storage::disk('uploads')->put($fullPath, $content);
            $file = Storage::disk('uploads')->get($fullPath);

            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');

            echo $file;
    }
    
    public function postBack(Request $request, $gateway) {
        $token = $request->input('auth_token');
        $gateways = get_payment_gateways();
        if (empty($gateways[$gateway]) or !class_exists($gateways[$gateway])) {
            $this->sendError(__("Payment gateway not found"));
        }
        $gatewayObj = new $gateways[$gateway]($gateway);
        if (!$gatewayObj->isAvailable()) {
            $this->sendError(__("Payment gateway is not available"));
        }
        return $gatewayObj->postBack($request);
    
        
    }

}
