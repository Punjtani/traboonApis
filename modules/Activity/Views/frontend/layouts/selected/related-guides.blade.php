<div class="bravo-list-activity-related-widget">
    <h3 class="heading">Related Guides</h3>
@foreach($relatedGuides as $guide)
<div class="item-loop-list"  id="item-loop-list-guides-{{$loop->index}}">
    <input class="activity_id" type="hidden" value="{{$guide['id']}}"/>
    <input class="price_id" type="hidden" value="{{$guide['price']}}"/>
    <div class="thumb-image">
        <a target="_blank" href="{{$guide['guide_link']}}">
           <img src="{{$guide['guide_img_link']}}" class="img-responsive" alt="">
        </a>
    </div>
    <div class="g-info">
        
        <div class="item-title">
            <a  target="_blank"  href="{{$guide['guide_link']}}">
                
                {{$guide['title']}}
            </a>
        </div>
        
        
        
        <div class="location">
            @if(!empty($guide['location']))
                
                <i class="icofont-paper-plane"></i>
                {{$guide['location'] ?? ''}}
            @endif
        </div>
    </div>
    <div class="g-rate-price">
        <a href="#" @click='addItemToSelected($event,"item-loop-list-guides-{{$loop->index}}","guide")'> <button class="btn btn-primary" title="Add to list"><span class="fa fa-plus-circle"></span></button></a>
        <div class="g-price">
        
        @if(setting_item('guide_enable_review'))            
            <div class="service-review-pc">
                <div class="head">
                    <div class="left">
                        <span class="head-rating">{{$guide['review_score']}}</span>
                        <span class="text-rating">{{__(":number reviews",['number'=>$guide['review_score']])}}</span>
                    </div>
                    <div class="score">
                        {{$guide['review_score']}}<span>/5</span>
                    </div>
                </div>
            </div>
        @endif
        
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $guide['price'] }} <span class="unit">{{__("/day")}}</span></span>
            </div>
            @if(!empty($guide['review_score']))
                <div class="text-review">
                    {{__(":number reviews",['number'=>$guide['review_score']])}}
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach
</div>