@extends('layouts.app')
@section('head')
    <link href="{{ asset('module/activity/css/activity.css?_ver='.config('app.version')) }}" rel="stylesheet">
    <link href="{{ asset('module/activity/css/temp.css?') }}" rel="stylesheet">
@endsection
@section('content')
    <div class="bravo_search_activity">
        <div class="bravo_content">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        @include('Activity::frontend.layouts.selected.related-hotels')
                        @include('Activity::frontend.layouts.selected.related-guides')
                    </div>
                    <div class="col-md-6 col-lg-4">
                        @include('Activity::frontend.layouts.selected.related-cars')
                        @include('Activity::frontend.layouts.selected.related-tours')
                    </div>
                    <div class="col-md-6 col-lg-3">
                        @include('Activity::frontend.layouts.selected.selected-sidebar')
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        @include('Activity::frontend.layouts.selected.checkout')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer') 
<script type="text/JavaScript">        
         var price = {{$totalCost}};
         var activities = {{$totalActivities}};
         var activities_ids = [
             @foreach($selectedActivities as $activity)
             {!!"{service_id:".$activity['id'].",service_type:'activity',service_price:".$activity['price']."},"!!}
         @endforeach
         ];
         
    </script>
    <script type="text/javascript" src="{{ asset('module/activity/js/activity-related.js?_ver='.config('app.version')) }}"></script>
    
@endsection