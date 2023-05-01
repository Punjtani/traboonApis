@if(count($activity_related) > 0)
    <div class="bravo-list-activity-related">
        <h2>{{__("You might also like")}}</h2>
        <div class="row">
            @foreach($activity_related as $k=>$item)
                <div class="col-md-3">
                    @include('Activity::frontend.layouts.search.loop-grid',['row'=>$item])
                </div>
            @endforeach
        </div>
    </div>
@endif