<div class="row" id="activity-selected">
    <div class="col-lg-3 col-md-12">
        @include('Activity::frontend.layouts.search.filter-search')
    </div>
    <div class="col-lg-6 col-md-12">
        <div class="bravo-list-item">
            <div class="topbar-search">
                <div class="text">
                    @if($rows->total() > 1)
                        {{ __(":count activities found",['count'=>$rows->total()]) }}
                    @else
                        {{ __(":count activity found",['count'=>$rows->total()]) }}
                    @endif
                </div>
                <div class="control">

                </div>
            </div>
            <div class="list-item">
                <div class="row">
                    @if($rows->total() > 0)
                        @foreach($rows as $row)
                            @php $layout = setting_item("activity_layout_item_search",'list') @endphp
                            @if($layout == "list")
                                <div class="col-lg-12 col-md-12">
                                    @include('Activity::frontend.layouts.search.loop-list')
                                </div>
                            @else
                                <div class="col-lg-4 col-md-12">
                                    @include('Activity::frontend.layouts.search.loop-grid')
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="col-lg-12">
                            {{__("Activity not found")}}
                        </div>
                    @endif
                </div>
            </div>
            <div class="bravo-pagination">
                {{$rows->appends(request()->query())->links()}}
                @if($rows->total() > 0)
                    <span class="count-string">{{ __("Showing :from - :to of :total Activities",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-12">
        @include('Activity::frontend.layouts.search.list-selected')
    </div>
</div>