@extends('layouts.app')
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <link href="{{ asset('module/hotel/css/hotel.css?_ver='.config('app.version')) }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/ion_rangeslider/css/ion.rangeSlider.min.css") }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset("libs/fotorama/fotorama.css") }}"/>
    <style>
    #panorama56 {
        width: 550px;
        height: 400px;
    }
    </style>
@endsection
@section('content')
    <div class="bravo_detail_hotel">
        @include('Hotel::frontend.layouts.details.hotel-banner')
        <div class="bravo_content">
            <div class="container">
                

                <div class="row">
                    <div class="col-md-12 col-lg-9">
                        @php $review_score = $row->review_data @endphp
                        @include('Hotel::frontend.layouts.details.hotel-detail')
                        @include('Hotel::frontend.layouts.details.hotel-review')
                    </div>
                    <div class="col-md-12 col-lg-3">
                        @include('Tour::frontend.layouts.details.vendor')
                        @include('Hotel::frontend.layouts.details.hotel-related-list')
                        <div class="g-all-attribute is_pc">
                            @include('Hotel::frontend.layouts.details.hotel-attributes')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Hotel::frontend.layouts.details.hotel-form-book-mobile')
    </div>
@endsection

@section('footer')
    {!! App\Helpers\MapEngine::scripts() !!}
    <script>
        jQuery(function ($) {
            @if($row->map_lat && $row->map_lng)
            new BravoMapEngine('map_content', {
                disableScripts: true,
                fitBounds: true,
                center: [{{$row->map_lat}}, {{$row->map_lng}}],
                zoom:{{$row->map_zoom ?? "8"}},
                ready: function (engineMap) {
                    engineMap.addMarker([{{$row->map_lat}}, {{$row->map_lng}}], {
                        icon_options: {}
                    });
                }
            });
            @endif
        })
    </script>
    <script>
        var bravo_booking_data = {!! json_encode($booking_data) !!}
        var bravo_booking_i18n = {
			no_date_select:'{{__('Please select Start and End date')}}',
            no_guest_select:'{{__('Please select at least one guest')}}',
            load_dates_url:'{{route('space.vendor.availability.loadDates')}}'
        };
    </script>
    
    
  
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script type="text/javascript" src="{{ asset("libs/ion_rangeslider/js/ion.rangeSlider.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("libs/fotorama/fotorama.js") }}"></script>
    <script type="text/javascript" src="{{ asset("libs/sticky/jquery.sticky.js") }}"></script>
    <script type="text/javascript" src="{{ asset('module/hotel/js/single-hotel.js?_ver='.config('app.version')) }}"></script>
    <script>
    pannellum.viewer('panorama56', {
        "type": "equirectangular",
        "panorama": "http://localhost/traboon/public/uploads/0000/22/2020/09/18/bryan-goff-iuyhxaia8ea-unsplash-150.jpg"
    });
    </script>
@endsection
