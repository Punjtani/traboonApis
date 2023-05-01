
<div class="bravo-list-activity-related-widget">
    <h3 class="heading">Selected Activities</h3>
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
    <div class="proceed"  v-if="total_price>0">
        <form method="post" action="{{url(config('activity.activity_route_prefix').'/selected_activities')}}" class="item-form" >
            {{ csrf_field() }}
            <template>
                <span v-for="ids in idsHtml" v-html="ids"></span>
            </template>
            
            
            <button type="submit" class="btn btn-primary">Proceed</button>       
        </form>
        
    </div>
</div>
    
