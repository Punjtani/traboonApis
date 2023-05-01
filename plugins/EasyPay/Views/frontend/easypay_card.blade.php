<div class="card_easypay">
    <i class="icofont-ui-v-card bg"></i>
    <div class="row">
        <label>
            <span>{{__("Name on the Card")}}</span>
            <input id="bravo_twocheckout_card_name" name="card_name" placeholder="{{__("Card Name")}}">
        </label>
        <label>
            <span>{{__("Card Number")}}</span>
            <input id="bravo_twocheckout_card_number" placeholder="{{__("Card Number")}}">
            <i class="icofont-credit-card"></i>
        </label>
    </div>
    <div class="row">
        <label class="item">
            <span>{{__("Expiration Month")}}</span>
            <input id="bravo_twocheckout_card_expiry_month" placeholder="{{__("Expiration Month")}}">
        </label>
        <label class="item">
            <span>{{__("Expiration Year")}}</span>
            <input id="bravo_twocheckout_card_expiry_year" placeholder="{{__("Expiration Year")}}">
        </label>
        <label class="item">
            <span>{{__("CVC")}}</span>
            <input id="bravo_twocheckout_card_cvc" placeholder="{{__("CVC")}}">
        </label>
    </div>
    
    
    <input name="token" type="hidden" value="" id="bravo_easypay_token"/>
    <div class="card_easypay_msg"></div>
</div>