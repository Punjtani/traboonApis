<?php
namespace Plugins\EasyPay\Gateway;

use Illuminate\Http\Request;
use Mockery\Exception;
use Modules\Booking\Models\Payment;
use Validator;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;

class EasyPayGateway extends \Modules\Booking\Gateways\BaseGateway
{
    protected $id   = 'easy_pay';
    public    $name = 'EasyPaisa EasyPay';
    private $postBackUrl = "https://traboon.com/booking/easypay-postback/EasyPay";
    private $confirmUrl = "https://traboon.com/booking/confirm/EasyPay";
    private $merchantPaymentMethod = 'MA_PAYMENT_METHOD';
    protected $gateway;

    public function getOptionsConfigs()
    {
        return [
            [
                'type'  => 'checkbox',
                'id'    => 'easypay_enable',
                'label' => __('Enable EasyPay?')
            ],
            [
                'type'  => 'input',
                'id'    => 'easypay_name',
                'label' => __('Custom Name'),
                'std'   => __("EasyPay")
            ],
            [
                'type'  => 'upload',
                'id'    => 'easypay_logo_id',
                'label' => __('Custom Logo'),
            ],
            [
                'type'  => 'editor',
                'id'    => 'easypay_html',
                'label' => __('Custom HTML Description')
            ],
            [
                'type'  => 'input',
                'id'    => 'easypay_store_id',
                'label' => __('Store ID'),
            ],
            [
                'type'  => 'input',
                'id'    => 'easypay_hash_key',
                'label' => __('Hash key'),
            ],
            [
                'type'  => 'input',
                'id'    => 'easypay_token_expiry',
                'label' => __('No. of Token Expiration Days'),
            ],
            [
                'type'  => 'checkbox',
                'id'    => 'easypay_sand',
                'label' => __('Enable sandbox?')
            ]
        ];
    }
public function isAvailable()
    {
        return $this->getOption('easypay_enable');
    }
    public function process(Request $request, $booking, $service)
    {
        if (in_array($booking->status, [
            $booking::PAID,
            $booking::COMPLETED,
            $booking::CANCELLED
        ])) {

            throw new Exception(__("Booking status does need to be paid"));
        }
        if (!$booking->total) {
            throw new Exception(__("Booking total is zero. Can not process payment gateway!"));
        }
        $payment = new Payment();
        $payment->booking_id = $booking->id;
        $payment->payment_gateway = $this->id;
        $payment->status = 'draft';
        $payment->save();
        $data = $this->handlePurchaseData([], $booking, $request);
        $booking->status = $booking::UNPAID;
        $booking->payment_id = $payment->id;
        $booking->save();
        if ($this->getOption('easypay_sand')) {
            $checkout_url_sandbox = 'https://easypaystg.easypaisa.com.pk/tpg/';
        } else {
            $checkout_url_sandbox = 'https://easypay.easypaisa.com.pk/tpg/';
        }
        $twoco_args = http_build_query($data, '', '&');
        response()->json([
            'url' => $checkout_url_sandbox . "?" . $twoco_args
        ])->send();
    }

    public function handlePurchaseData($data, $booking, $request)
    {
        
        $easypay_args = array();
        $easypay_args['storeId'] = $this->getOption('easypay_store_id');
        $easypay_args['orderId'] = $booking->code;
        $easypay_args['transactionAmount'] = (float)$booking->total;
        $easypay_args['mobileAccountNo'] = $request->input("mobileAccountNo");
        $easypay_args['emailAddress'] = $request->input("emailAddress");
        $easypay_args['transactionType']  = "InitialRequest" ;
        date_default_timezone_set('Asia/Karachi');
        $expiryDate = '';
    	$currentDate = new \DateTime();
    	$currentDate->modify('+'.$this->getOption('easypay_token_expiry').'day');
	$expiryDate = $currentDate->format('Ymd His');
	$easypay_args['tokenExpiry'] = $expiryDate;
        $easypay_args['timeStamp'] = date("Y-m-d\TH:i:00");
        $easypay_args['bankIdentificationNumber'] = '';
    	
        $easypay_args['merchantPaymentMethod'] = $this->merchantPaymentMethod;
        $easypay_args['postBackURL'] = $this->postBackUrl;
        $easypay_args['signature'] = '';
        $easypay_args['encryptedHashRequest'] = $this->getHashedRequest($this->getOption('easypay_hash_key'), $easypay_args['orderId'], 
        $easypay_args['transactionAmount'], $easypay_args['emailAddress'], $easypay_args['mobileAccountNo'], 
        $easypay_args['tokenExpiry'], $easypay_args['storeId'], $this->merchantPaymentMethod, $this->postBackUrl,
        $easypay_args['timeStamp']);
        //$paramMap['timeStamp']  =         
        //$easypay_args['bankIdentificationNumber'] = '';
        // $easypay_args['currency_code'] = setting_item('currency_main');
        // $easypay_args['card_holder_name'] = $request->input("first_name") . ' ' . $request->input("last_name");
        // $easypay_args['street_address'] = $request->input("address_line_1");
        // $easypay_args['street_address2'] = $request->input("address_line_1");
        // $easypay_args['city'] = $request->input("city");
        // $easypay_args['state'] = $request->input("state");
        // $easypay_args['country'] = $request->input("country");
        // $easypay_args['zip'] = $request->input("zip_code");
        // $easypay_args['phone'] = "";
        // $easypay_args['email'] = $request->input("email");
        // $easypay_args['lang'] = app_get_locale();
        return $easypay_args;
    }
    
    /**
    * getHashedRequest() Function to generate EasyPay Hash
    **/
    public function getHashedRequest($hashKey, $orderId, 
            $amount,$custEmail,$custCell,$daysToExpire,
            $storeId,$paymentMethodVal,$merchantConfirmPage,$timeStamp) {

	if (strpos($amount, '.') !== false) {
		$amount = $amount;
	} else {
		$amount = sprintf("%0.1f",$amount);
	}
	//$paymentMethodVal = "MA_PAYMENT_METHOD";
	
	$hashRequest = '';
	if(strlen($hashKey) > 0 && (strlen($hashKey) == 16 || strlen($hashKey) == 24 || strlen($hashKey) == 32 )) {
		// Create Parameter map
		$paramMap = array();
                $paramMap['amount']  = $amount;
                $paramMap['expiryDate'] = $daysToExpire;
                $paramMap['orderRefNum']  = $orderId ;
                $paramMap['paymentMethod']  = "InitialRequest" ;
                $paramMap['storeId']  = $storeId ;
                $paramMap['timeStamp']  = $timeStamp;
//                if($paymentMethodVal != null && $paymentMethodVal != '') {
//			$paramMap['merchantPaymentMethod']  = $paymentMethodVal ;
//		}
                
		
                
//                if($custCell != null && $custCell != '') {
//			$paramMap['mobileNum'] = $custCell;
//		}
//		if($custEmail != null && $custEmail != '') {
//			$paramMap['emailAddress']  = $custEmail ;
//		}
                
		
		
		
		
		
//		$paramMap['postBackURL'] = $merchantConfirmPage;
		
		
		
		//Creating string to be encoded
		$mapString = '';
		foreach ($paramMap as $key => $val) {
			$mapString .=  $key.'='.$val.'&';
		}
		$mapString  = substr($mapString , 0, -1);
		
		// Encrypting mapString
		
		$cipher = "aes-128-ecb";
		$crypttext = openssl_encrypt($mapString, $cipher, $hashKey,OPENSSL_RAW_DATA);
		$hashRequest = base64_encode($crypttext);
		return $hashRequest;
	}
    }
    public function getDisplayHtml()
    {
        return view('EasyPay::frontend/easypay_ma');
    }
    public function postBack(Request $request) {
        $token = $request->input('auth_token');
        $easypayConfirmPage = '';
        
        if ($this->getOption('easypay_sand')) {
                $easypayConfirmPage = 'https://easypaystg.easypaisa.com.pk/easypay/Confirm.jsf';
        } else {
                $easypayConfirmPage = 'https://easypaystg.easypaisa.com.pk/easypay/Confirm.jsf';
        }

        $response='<form name="easypayconfirmform" action="'.$easypayConfirmPage.'" method="POST">
            <input name="auth_token" value="'.$token.'" hidden = "true"/>
            <input name="postBackURL" value="'.$this->confirmUrl.'" hidden = "true"/>	
        </form>

        <script data-cfasync="false" type="text/javascript">
            document.easypayconfirmform.submit();
        </script>';
        return $response;
    
        
    }
    public function confirmPayment(Request $request)
    {
        $c = $request->query('orderRefNumber');
        $booking = Booking::where('code', $c)->first();
        if (!empty($booking) and in_array($booking->status, [$booking::UNPAID])) {
//            $compare_string = $this->getOption('twocheckout_secret_word') . $this->getOption('twocheckout_account_number') . $request->input("order_number") . $request->input("total");
//            $compare_hash1 = strtoupper(md5($compare_string));
//            $compare_hash2 = $request->input("key");
//            if ($compare_hash1 != $compare_hash2) {
//                $payment = $booking->payment;
//                if ($payment) {
//                    $payment->status = 'fail';
//                    $payment->logs = \GuzzleHttp\json_encode($request->input());
//                    $payment->save();
//                }
//                try {
//                    $booking->markAsPaymentFailed();
//                } catch (\Swift_TransportException $e) {
//                    Log::warning($e->getMessage());
//                }
//                return redirect($booking->getDetailUrl())->with("error", __("Payment Failed"));
//            } else {
                $payment = $booking->payment;
                if ($payment) {
                    $payment->status = 'completed';
                    $payment->logs = \GuzzleHttp\json_encode($request->input());
                    $payment->save();
                }
                try {
                    $booking->markAsPaid();
                } catch (\Swift_TransportException $e) {
                    Log::warning($e->getMessage());
                }
                return redirect($booking->getDetailUrl())->with("success", __("You payment has been processed successfully"));
            
        }
        if (!empty($booking)) {
            return redirect($booking->getDetailUrl(false));
        } else {
            return redirect(url('/'));
        }
    }
    
    public function cancelPayment(Request $request)
    {
        $c = $request->query('c');
        $booking = Booking::where('code', $c)->first();
        if (!empty($booking) and in_array($booking->status, [$booking::UNPAID])) {
            $payment = $booking->payment;
            if ($payment) {
                $payment->status = 'cancel';
                $payment->logs = \GuzzleHttp\json_encode([
                    'customer_cancel' => 1
                ]);
                $payment->save();
            }
            return redirect($booking->getDetailUrl())->with("error", __("You cancelled the payment"));
        }
        if (!empty($booking)) {
            return redirect($booking->getDetailUrl());
        } else {
            return redirect(url('/'));
        }
    }
    
     
}