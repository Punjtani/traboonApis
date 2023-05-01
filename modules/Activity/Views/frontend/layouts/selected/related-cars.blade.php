<div class="bravo-list-activity-related-widget">
    <h3 class="heading">Related Cars</h3>
@foreach($relatedCars as $car)
<div class="item-loop-list"  id="item-loop-list-cars-{{$loop->index}}">
    <input class="activity_id" type="hidden" value="{{$car['id']}}"/>
    <input class="price_id" type="hidden" value="{{$car['price']}}"/>
    <div class="thumb-image">
        <a target="_blank" href="{{$car['car_link']}}">
           <img src="{{$car['car_img_link']}}" class="img-responsive" alt="">
        </a>
    </div>
    <div class="g-info">
        
        <div class="item-title">
            <a  target="_blank"  href="{{$car['car_link']}}">
                
                {{$car['title']}}
            </a>
        </div>
        
        
        
        <div class="location">
            @if(!empty($car['location']))
                
                <i class="icofont-paper-plane"></i>
                {{$car['location'] ?? ''}}
            @endif
        </div>
    </div>
    <div class="g-rate-price">
        <a href="#" @click='addItemToSelected($event,"item-loop-list-cars-{{$loop->index}}","car")'> <button class="btn btn-primary" title="Add to list"><span class="fa fa-plus-circle"></span></button></a>
        <div class="g-price">
        
        @if(setting_item('car_enable_review'))            
            <div class="service-review-pc">
                <div class="head">
                    <div class="left">
                        <span class="head-rating">{{$car['review_score']}}</span>
                        <span class="text-rating">{{__(":number reviews",['number'=>$car['review_score']])}}</span>
                    </div>
                    <div class="score">
                        {{$car['review_score']}}<span>/5</span>
                    </div>
                </div>
            </div>
        @endif
        
            <div class="prefix">
                <span class="fr_text">{{__("from")}}</span>
            </div>
            <div class="price">
                <span class="text-price">{{ $car['price'] }} <span class="unit">{{__("/night")}}</span></span>
            </div>
            @if(!empty($car['review_score']))
                <div class="text-review">
                    {{__(":number reviews",['number'=>$car['review_score']])}}
                </div>
            @endif
        </div>
    </div>
</div>
@endforeach
</div>