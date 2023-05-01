@if(count($guide_related) > 0)
    <div class="bravo-list-guide-related">
        <h2>{{__("You might also like")}}</h2>
        <div class="row">
            @foreach($guide_related as $k=>$item)
                <div class="col-md-3">
                    @include('Guide::frontend.layouts.search.loop-grid',['row'=>$item])
                </div>
            @endforeach
        </div>
    </div>
@endif