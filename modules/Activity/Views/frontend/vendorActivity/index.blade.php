@extends('layouts.user')
@section('head')

@endsection
@section('content')
    <h2 class="title-bar">
        {{__("Manage Activitys")}}
        @if(Auth::user()->hasPermissionTo('activity_create'))
            <a href="{{ route("activity.vendor.create") }}" class="btn-change-password">{{__("Add Activity")}}</a>
        @endif
    </h2>
    @include('admin.message')
    @if($rows->total() > 0)
        <div class="bravo-list-item">
            <div class="bravo-pagination">
                <span class="count-string">{{ __("Showing :from - :to of :total Activitys",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                {{$rows->appends(request()->query())->links()}}
            </div>
            <div class="list-item">
                <div class="row">
                    @foreach($rows as $row)
                        <div class="col-md-12">
                            @include('Activity::frontend.vendorActivity.loop-list')
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bravo-pagination">
                <span class="count-string">{{ __("Showing :from - :to of :total Activitys",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                {{$rows->appends(request()->query())->links()}}
            </div>
        </div>
    @else
        {{__("No Activity")}}
    @endif
@endsection
@section('footer')

@endsection