<?php
return [
    'booking_route_prefix'=>env("BOOKING_ROUTER_PREFIX",'booking'),
    'services'=>[
        'tour'=>Modules\Tour\Models\Tour::class
    ],
    'payment_gateways'=>[
        'offline_payment'=>Modules\Booking\Gateways\OfflinePaymentGateway::class,
    ],
    'statuses'=>[
        'completed',
        'processing',
        'confirmed',
        'cancelled',
        'paid',
        'unpaid',
    ]
];
