@php $lang_local = app()->getLocale() @endphp
@php $total=0 @endphp

<div class="booking-review">
    <h4 class="booking-review-title">{{__("Your Booking")}}</h4>
    <div class="booking-review-content">
        @php
        $grandTotal = 0;
        @endphp
        @foreach($bookings as $booking)
            @php
            $total = $booking->total;
            @endphp
        <div class="review-section">
            <div class="service-info">
                <div>
                    @php
                        $service_translation = $booking->service->translateOrOrigin($lang_local);
                    @endphp
                    <h3 class="service-name"><a href="{{$booking->service->getDetailUrl()}}">{{$service_translation->title}}</a></h3>
                    @if($service_translation->address)
                        <p class="address"><i class="fa fa-map-marker"></i>
                            {{$service_translation->address}}
                        </p>
                    @endif
                </div>
                <div>
                    @if($image_url = $booking->service->image_url)
                        @if(!empty($disable_lazyload))
                            <img src="{{$booking->service->image_url}}" class="img-responsive" alt="">
                        @else
                            {!! get_image_tag($booking->service->image_id,'medium',['class'=>'img-responsive','alt'=>$booking->service->title]) !!}
                        @endif

                    @endif
                </div>
            </div>
        </div>
        
        <div class="review-section">
            <ul class="review-list">
                @if($booking->start_date)
                    <li>
                        <div class="label">{{__('Start date:')}}</div>
                        <div class="val">
                            {{display_date($booking->start_date)}}
                        </div>
                    </li>
                    <li>
                        <div class="label">{{__('End date:')}}</div>
                        <div class="val">
                            {{display_date($booking->end_date)}}
                        </div>
                    </li>
                    <li>
                        <div class="label">{{__('Days:')}}</div>
                        <div class="val">
                            {{$booking->duration_days}}
                        </div>
                    </li>
                @endif
                @if($meta = $booking->number)
                    <li>
                        <div class="label">{{__('Number:')}}</div>
                        <div class="val">
                            {{$meta}}
                        </div>
                    </li>
                @endif
            </ul>
        </div>
        {{--@include('Booking::frontend/booking/checkout-coupon')--}}
        <div class="review-section total-review">
            <ul class="review-list">
                
                @php $extra_price = $booking->getJsonMeta('extra_price') @endphp
                @if(!empty($extra_price))
                    <li>
                        <div class="label-title"><strong>{{__("Extra Prices:")}}</strong></div>
                    </li>
                    <li class="no-flex">
                        <ul>
                            @foreach($extra_price as $type)
                                <li>
                                    <div class="label">{{$type['name_'.$lang_local] ?? $type['name']}}:</div>
                                    <div class="val">
                                        {{format_money($type['total'] ?? 0)}}
                                        
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                
                @if(!empty($booking->buyer_fees))
                    <?php
                    $buyer_fees = json_decode($booking->buyer_fees , true);
                    foreach ($buyer_fees as $buyer_fee){
                        ?>
                        <li>
                            <div class="label">
                                {{$buyer_fee['name_'.$lang_local] ?? $buyer_fee['name']}}
                                <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top" title="{{ $buyer_fee['desc_'.$lang_local] ?? $buyer_fee['desc'] }}"></i>
                                @if(!empty($buyer_fee['per_person']) and $buyer_fee['per_person'] == "on")
                                    : {{$booking->total_guests}} * {{format_money( $buyer_fee['price'] )}}
                                @endif
                            </div>
                            <div class="val">
                                @if(!empty($buyer_fee['per_person']) and $buyer_fee['per_person'] == "on")
                                    {{ format_money( $buyer_fee['price'] * $booking->total_guests ) }}
                                    $total+=$buyer_fee['price'] * $booking->total_guests @endphp
                                @else
                                {{ format_money($buyer_fee['price']) }}
                                @php $total+=$buyer_fee['price'] @endphp
                                @endif
                            </div>
                        </li>
                    <?php } ?>
                @endif
                <li class="final-total">
                    <div class="label">{{__("Total:")}}</div>
                    <div class="val">{{format_money($total)}}</div>
                </li>
            </ul>
        </div>
        @php
            $grandTotal += $total;
        @endphp
        
        @endforeach
        <div class="grand-total">
            <div class="label">{{__("Grand Total:")}}</div>
            <div class="val">{{format_money($grandTotal)}}</div>
        </div>
    </div>
</div>