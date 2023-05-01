@extends('layouts.app')
@section('head')
    <link href="{{ asset('module/booking/css/checkout.css?_ver='.config('app.version')) }}" rel="stylesheet">
@endsection
@section('content')
    <div class="bravo-booking-page padding-content" >
        <div class="container">
            <div id="bravo-checkout-page" >
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="form-title">{{__('Booking Submission')}}</h3>
                         <div class="booking-form">
                             @include ('Booking::frontend/booking/checkout-form-many')

                         </div>
                    </div>
                    <div class="col-md-4">
                        <div class="booking-detail">
                            
                                @include ('Activity::frontend/booking/detail')
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    <script src="{{ asset('module/booking/js/checkout-bulk.js') }}"></script>
    <script type="text/javascript">
        jQuery(function () {
            $.ajax({
                'url': bookingCore.url + '/booking/{{$bulk_id}}/check-status',
                'cache': false,
                'type': 'GET',
                success: function (data) {
                    if (data.redirect !== undefined && data.redirect) {
                        window.location.href = data.redirect
                    }
                }
            });
        })
    </script>
@endsection