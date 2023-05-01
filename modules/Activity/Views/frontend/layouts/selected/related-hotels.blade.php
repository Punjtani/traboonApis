<div class="bravo-list-activity-related-widget">
    <h3 class="heading">Related Hotels</h3>
@foreach($relatedHotels as $hotel)
<div class="item-loop-list"  id="item-loop-list-hotels-{{$loop->index}}">
    <input class="activity_id" type="hidden" value="{{$hotel['id']}}"/>
    <input class="price_id" type="hidden" value="{{$hotel['price']}}"/>
    <div class="thumb-image">
        <a target="_blank" href="{{$hotel['hotel_link']}}">
           <img src="{{$hotel['hotel_img_link']}}" class="img-responsive" alt="">
        </a>
    </div>
    <div class="g-info">
        @if($hotel['star_rate'])
            <div class="star-rate">
                <div class="list-star">
                    <ul class="booking-item-rating-stars">
                        @for ($star = 1 ;$star <= $hotel['star_rate'] ; $star++)
                            <li><i class="fa fa-star"></i></li>
                        @endfor
                    </ul>
                </div>
            </div>
        @endif
        <div class="item-title">
            <a  target="_blank"  href="{{$hotel['hotel_link']}}">
                
                {{$hotel['title']}}
            </a>
        </div>
        
        
        
        <div class="location">
            @if(!empty($hotel['location']))
                
                <i class="icofont-paper-plane"></i>
                {{$hotel['location'] ?? ''}}
            @endif
        </div>
    </div>
    <div class="g-rate-price">
        <a href="#" @click='addItemToSelected($event,"item-loop-list-hotels-{{$loop->index}}","hotel")'> <button class="btn btn-primary" title="Add to list"><span class="fa fa-plus-circle"></span></button></a>
        <div class="g-price">
        
        @if(setting_item('hotel_enable_review'))            
            <div class="service-review-pc">
                <div class="head">
                    <div class="left">
                        <span class="head-rating">{{$hotel['review_score']}}</span>
                        <span class="text-rating">{{__(":number reviews",['number'=>$hotel['review_score']])}}</span>
                    </div>
                    <div class="score">
                        {{$hotel['review_score']}}<span>/5</span>
                    </div>
                </div>
            </div>
        @endif
        
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $hotel['price'] }} <span class="unit">{{__("/night")}}</span></span>
            </div>
            @if(!empty($hotel['review_score']))
                <div class="text-review">
                    {{__(":number reviews",['number'=>$hotel['review_score']])}}
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach
</div>