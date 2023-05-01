<div class="bravo-list-activity-related-widget">
    <h3 class="heading">Related Tours</h3>
@foreach($relatedTours as $tour)
<div class="item-loop-list"  id="item-loop-list-tours-{{$loop->index}}">
    <input class="activity_id" type="hidden" value="{{$tour['id']}}"/>
    <input class="price_id" type="hidden" value="{{$tour['price']}}"/>
    <div class="thumb-image">
        <a target="_blank" href="{{$tour['tour_link']}}">
           <img src="{{$tour['tour_img_link']}}" class="img-responsive" alt="">
        </a>
    </div>
    <div class="g-info">
        
        <div class="item-title">
            <a  target="_blank"  href="{{$tour['tour_link']}}">
                
                {{$tour['title']}}
            </a>
        </div>
        
        
        
        <div class="location">
            @if(!empty($tour['location']))
                
                <i class="icofont-paper-plane"></i>
                {{$tour['location'] ?? ''}}
            @endif
        </div>
    </div>
    <div class="g-rate-price">
        <a href="#" @click='addItemToSelected($event,"item-loop-list-tours-{{$loop->index}}","tour")'> <button class="btn btn-primary" title="Add to list"><span class="fa fa-plus-circle"></span></button></a>
        <div class="g-price">
        
        @if(setting_item('tour_enable_review'))            
            <div class="service-review-pc">
                <div class="head">
                    <div class="left">
                        <span class="head-rating">{{$tour['review_score']}}</span>
                        <span class="text-rating">{{__(":number reviews",['number'=>$tour['review_score']])}}</span>
                    </div>
                    <div class="score">
                        {{$tour['review_score']}}<span>/5</span>
                    </div>
                </div>
            </div>
        @endif
        
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $tour['price'] }} <span class="unit">{{__("/night")}}</span></span>
            </div>
            @if(!empty($tour['review_score']))
                <div class="text-review">
                    {{__(":number reviews",['number'=>$tour['review_score']])}}
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach
</div>