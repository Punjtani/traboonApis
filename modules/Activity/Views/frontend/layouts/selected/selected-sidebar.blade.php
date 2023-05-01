
<div class="bravo-list-activity-related-widget">
    <h3 class="heading">Selected Items</h3>
    
    @foreach($selectedActivities as $activity)
    
    <div class="list-item">                                        
        <div class="item">
            
            <div class="media">
                
                <div class="media-left">
                    <a href="{{$activity['activity_link']}}" target="_blank">                                                                                                        
                            <img class="img-responsive lazy loaded" data-src="{{$activity['activity_img_link']}}" alt="{{$activity['title']}}" src="{{$activity['activity_img_link']}}" data-was-processed="true">                                                                                            
                        </a>                    
                </div>                    
                <div class="media-body">
                                                    
                    <h4 class="media-heading">
                            <a href="{{$activity['activity_link']}}" target="_blank">
                                {{$activity['title']}}
                            </a>                        
                    </h4>
                    <div class="price-wrapper">
                            from
                        <span class="price">₨{{$activity['price']}}</span>
                        <span class="unit">{{_("/activity")}}</span>                        
                    </div>                    
                </div>                
            </div>            
        </div>  
        
    </div>
    @endforeach
    <div class="list-item"  v-for="(input, index) in html">                                        
        <div class="item">
            <span @click='removeActivityFromSelected(index)' title="Remove activity" class="fa fa-times-circle item-remove"></span>
            <div class="media">
                
                <div class="media-left">
                        <a v-bind:href="input.item_url">                                                                                                        
                            <img class="img-responsive lazy loaded" v-bind:data-src="input.item_pic_url" v-bind:alt="input.item_title" v-bind:src="input.item_pic_url" data-was-processed="true">                                                                                            
                        </a>                    
                </div>                    
                <div class="media-body">
                                                    
                    <h4 class="media-heading">
                            <a v-bind:href="input.item_url">
                                @{{input.item_title}}
                            </a>                        
                    </h4>
                    <div class="price-wrapper">
                            from
                        <span class="price">₨@{{input.item_price}}</span>
                        <span class="unit">@{{input.item_unit}}</span>                        
                    </div>                    
                </div>                
            </div>            
        </div>  
        
    </div>
    <div class="total-price" v-if="total_price>0">
        <span class="total-activity-text">
            Activities: <b>@{{total_activities}}</b><br/>
        </span>
        <span class="total-price-text">
            <b>Total Price:</b> Rs. @{{total_price}}
        </span>
    </div>
    
    <div class="proceed">
        <button @click="doProceed" type="submit" class="btn btn-primary">
            Proceed to Checkout
            
        </button>
    </div>
</div>
    