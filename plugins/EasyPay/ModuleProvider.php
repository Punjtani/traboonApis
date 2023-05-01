<?php
namespace Plugins\EasyPay;
use Modules\ModuleServiceProvider;
use Plugins\EasyPay\Gateway\EasyPayGateway;

class ModuleProvider extends ModuleServiceProvider
{
    public function register()
    {
        $this->app->register(RouterServiceProvider::class);
    }

    public static function getPaymentGateway()
    {
        return [
            'easy_pay' => EasyPayGateway::class
        ];
    }

    public static function getPluginInfo()
    {
        return [
            'title'   => __('Gateway EasyPay'),
            'desc'    => __('Gateway EasyPay is one of the best payment Gateway to accept online payments from buyers around the world which allow your customers to make purchases in many payment methods. Luckily, this is a Pakistani gateway.'),
            'author'  => "Muhammad Ismail - https://www.linkedin.com/in/muhammad-ismail-63253847/",
            'version' => "1.0.0",
        ];
    }
}
