
<form action="{{ route("activity.search") }}" class="form bravo_form" method="get">
    <div class="g-field-search">
        <div class="row">
            <div class="col-md-4 border-right">
                <div class="form-group">
                    <i class="field-icon fa icofont-map"></i>
                    <div class="form-content">
                        <label>{{__("Location")}}</label>
                        <?php
                        $location_name = "";
                        $list_json = [];
                        $traverse = function ($locations, $prefix = '') use (&$traverse, &$list_json , &$location_name) {
                            foreach ($locations as $location) {
                                $translate = $location->translateOrOrigin(app()->getLocale());
                                if (Request::query('location_id') == $location->id){
                                    $location_name = $translate->name;
                                }
                                $list_json[] = [
                                    'id' => $location->id,
                                    'title' => $prefix . ' ' . $translate->name,
                                ];
                                $traverse($location->children, $prefix . '-');
                            }
                        };
                        $traverse($list_location);
                        ?>
                        <div class="smart-search">
                            <input type="text" class="smart-search-location parent_text form-control" {{ ( empty(setting_item("activity_location_search_style")) or setting_item("activity_location_search_style") == "normal" ) ? "readonly" : ""  }} placeholder="{{__("Where are you going?")}}" value="{{ $location_name }}" data-onLoad="{{__("Loading...")}}"
                                   data-default="{{ json_encode($list_json) }}">
                            <input type="hidden" class="child_id" name="location_id" value="{{Request::query('location_id')}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 border-right">
                <div class="form-group">
                    <i class="field-icon icofont-wall-clock"></i>
                    <div class="form-content">
                        <div class="form-date-search-activity">
                            <div class="date-wrapper">
                                <div class="check-in-wrapper">
                                    <label>{{__("Check In - Out")}}</label>
                                    <div class="render check-in-render">{{Request::query('start',display_date(strtotime("today")))}}</div>
                                    <span> - </span>
                                    <div class="render check-out-render">{{Request::query('end',display_date(strtotime("+1 day")))}}</div>
                                </div>
                            </div>
                            <input type="hidden" class="check-in-input" value="{{Request::query('start',display_date(strtotime("today")))}}" name="start">
                            <input type="hidden" class="check-out-input" value="{{Request::query('end',display_date(strtotime("+1 day")))}}" name="end">
                            <input type="hidden" class="check-in-out" name="date" value="{{Request::query('date',date("Y-m-d")." - ".date("Y-m-d",strtotime("+1 day")))}}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 border-right dropdown form-select-age">
                <div class="form-group">
                    <i class="field-icon icofont-travelling"></i>
                    <div class="form-content dropdown-toggle" data-toggle="dropdown">
                        <div class="wrapper-more">
                            <label>{{__('Duration')}}</label>
                            @php
                                $min_age = request()->query('min_duration',2);
                                $max_age = request()->query('max_duration',20);
                            @endphp
                            <div class="render">
                                <span class="min-age" ><span class="one" data-html="{{__(':count')}}">{{$min_age}}</span> </span>
                                -
                                <span class="max-age" >
                            <span class="one"  data-html="{{__(':count')}}">{{$max_age}}</span>
                            
                        </span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-menu select-age-dropdown" >
                        <input type="hidden" name="min_age" value="{{request()->query('min_age',18)}}" min="18" max="80">
                        <input type="hidden" name="max_age" value="{{request()->query('max_age',50)}}" min="18" max="80">
                        
                        <div class="dropdown-item-row">
                            <div class="label">{{__('Min age')}}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            
                            </div>
                            <div class="val">
                                <span class="btn-minus" data-input="min_age"><i class="icon ion-md-remove"></i></span>
                                <span class="count-display">{{request()->query('min_age',18)}}</span>
                                <span class="btn-add" data-input="min_age"><i class="icon ion-ios-add"></i></span>
                            </div>
                        </div>
                        <div class="dropdown-item-row">
                            <div class="label">{{__('Max age')}}</div>
                            <div class="val">
                                <span class="btn-minus" data-input="max_age"><i class="icon ion-md-remove"></i></span>
                                <span class="count-display">{{request()->query('max_age',50)}}</span>
                                <span class="btn-add" data-input="max_age"><i class="icon ion-ios-add"></i></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <!--
            <div class="col-md-4 border-right dropdown form-select-guests">
                <div class="form-group">
                    <i class="field-icon icofont-travelling"></i>
                    <div class="form-content">
                        <div class="wrapper-more">
                            <label>Age</label>
                            <input min="20" type="number" name="age" class="form-control" style="height:25px;">
                        </div>
                    </div>
                    
                </div>
            </div>
            -->
        </div>
    </div>
    <div class="g-button-submit">
        <button class="btn btn-primary btn-search" type="submit">{{__("Search")}}</button>
    </div>
</form>