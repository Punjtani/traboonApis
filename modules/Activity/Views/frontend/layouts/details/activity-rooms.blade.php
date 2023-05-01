<div id="activity-rooms" class="activity_rooms_form" v-cloak="">
    <h3 class="heading-section">{{__('Activity Availability')}}</h3>
    <div class="form-search-rooms">
        <div class="d-flex form-search-row">
            <div class="col-md-4">
                <div class="form-group form-date-field form-date-search " @click="openStartDate" data-format="{{get_moment_date_format()}}">
                    <i class="fa fa-angle-down arrow"></i>
                    <input type="text" class="start_date" ref="start_date" style="height: 1px; visibility: hidden">
                    <div class="date-wrapper form-content" >
                        <label class="form-label">{{__("Check In - Out")}}</label>
                        <div class="render check-in-render" v-html="start_date_html"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-btn">
                <div class="g-button-submit">
                    <button class="btn btn-primary btn-search" @click="checkAvailability" :class="{'loading':onLoadAvailability}" type="submit">
                        {{__("Check Availability")}}
                        <i v-show="onLoadAvailability" class="fa fa-spinner fa-spin"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="start_room_sticky"></div>
    <div class="activity_list_rooms" :class="{'loading':onLoadAvailability}">
        
    </div>
    <div class="activity_room_book_status" v-if="total_price">
        <div class="row">
            <div class="col-md-6">
                <div class="extra-price-wrap d-flex justify-content-between">
                    <div class="flex-grow-1">
                        <label>
                            {{__("Total Days")}}:
                        </label>
                    </div>
                    <div class="flex-shrink-0">
                        @{{total_days}}
                    </div>
                </div>
                <div class="extra-price-wrap d-flex justify-content-between" v-for="(type,index) in buyer_fees">
                    <div class="flex-grow-1">
                        <label>
                            @{{type.type_name}}
                            <span class="render" v-if="type.price_type">(@{{type.price_type}})</span>
                            <i class="icofont-info-circle" v-if="type.desc" data-toggle="tooltip" data-placement="top" :title="type.type_desc"></i>
                        </label>
                    </div>
                    <div class="flex-shrink-0">@{{formatMoney(type.price)}}
                    </div>
                </div>
                <div class="extra-price-wrap d-flex justify-content-between is_mobile">
                    <div class="flex-grow-1">
                        <label>
                            {{__("Total Price")}}:
                        </label>
                    </div>
                    <div class="total-room-price">@{{total_price_html}}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="control-book">
                    <div class="total-room-price">
                        <span> {{__("Total Price")}}:</span> @{{total_price_html}}
                    </div>
                    <button type="button" class="btn btn-primary" @click="doSubmit($event)" :class="{'disabled':onSubmit}" name="submit">
                        <span >{{__("Book Now")}}</span>
                        <i v-show="onSubmit" class="fa fa-spinner fa-spin"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>
    <div class="end_room_sticky"></div>
    <div class="alert alert-warning" v-if="!firstLoad && !rooms.length">
        {{__("No room available with your selected date. Please change your search critical")}}
    </div>
</div>
