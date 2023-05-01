jQuery(function ($) {
    $(".bravo_filter .g-filter-item").each(function () {
        if($(window).width() <= 990){
            $(this).find(".item-title").toggleClass("e-close");
        }
        $(this).find(".item-title").click(function () {
            $(this).toggleClass("e-close");
            if($(this).hasClass("e-close")){
                $(this).closest(".g-filter-item").find(".item-content").slideUp();
            }else{
                $(this).closest(".g-filter-item").find(".item-content").slideDown();
            }
        });
        $(this).find(".btn-more-item").click(function () {
            $(this).closest(".g-filter-item").find(".hide").removeClass("hide");
            $(this).addClass("hide");
        });
        $(this).find(".bravo-filter-price").each(function () {
            var input_price = $(this).find(".filter-price");
            var min = input_price.data("min");
            var max = input_price.data("max");
            var from = input_price.data("from");
            var to = input_price.data("to");
            var symbol = input_price.data("symbol");
            input_price.ionRangeSlider({
                type: "double",
                grid: true,
                min: min,
                max: max,
                from: from,
                to: to,
                prefix: symbol
            });
        });
    });
    $(".bravo_form_filter input[type=checkbox]").change(function () {
        $(this).closest(".bravo_form_filter").submit();
    });
var activityForm = new Vue({
        el:'#activity-selected',
        data:{
            total_price:0,
            total_activities:0,
            html:[],
            idsHtml:[],
        },
        watch:{
            extra_price:{
                
            },
            
        },
        computed:{
            total_days:function(){
//                var me = this;
//                if (me.start_date !== "") {
//                    var t = 0;
//                    _.forEach(this.rooms,function (item) {
//                        if(item.days){
//                            t += parseInt(item.days);
//                        }
//                    })
//                    return t;
//                }
//                return 0;
            },
            total_price_html:function(){
//                if(!this.total_price) return '';
//                setTimeout(function () {
//                    $('[data-toggle="tooltip"]').tooltip();
//                    $(document).trigger("scroll");
//                },200);
//                return window.bravo_format_money(this.total_price);
            },
        },
        
        methods:{
            addActivityToSelected:function(event,parentClass) {
                event.preventDefault();
                //alert($("#"+parentClass).find(".text-price").html().toString())
                console.log(this.html.findIndex(x => x.id == parentClass))
                if(this.html.findIndex(x => x.id == parentClass)>-1)
                {
                    bookingCoreApp.showError("Item already selected");
                }
                else
                {
                    this.html.push({
                    id:parentClass,
                    item_pic_url: $("#"+parentClass).find(".thumb-image").find("img").attr("src"),
                    item_url: $("#"+parentClass).find(".thumb-image").find("a").attr("href"),
                    item_title: $("#"+parentClass).find(".item-title").find("a").html(),
                    item_price: parseInt($("#"+parentClass).find(".text-price").text().match(/[0-9]+/)),
                    item_unit:$("#"+parentClass).find(".unit").html(),
                    });
                    this.total_price += parseInt($("#"+parentClass).find(".text-price").html().match(/[0-9]+/));
                    this.total_activities++;
                    this.idsHtml.push('<input type="hidden" value="'+$("#"+parentClass).find(".activity_id").val()+'" name="activity_ids[]"/>');
                    //$(".bravo-list-activity-related-widget").find(".item-form").append('<input type="hidden" value="'+$("#"+parentClass).find(".activity_id").val()+'" name="activity_ids[]"/>');
                    
                       // console.log($("#"+parentClass).find(".activity_id").val());
                    
//                    $.ajax({
//                        url:bookingCore.url+'/activity/checkAvailability',
//                        data:{
//                            activity_id:this.id,
////                            start_date:this.start_date,
////                            end_date:this.end_date,
////                            firstLoad:me.firstLoad,
////                            age:this.age,
//                        },
//                        method:'post',
//                        success:function (json) {
//                            //console.log(json);
//                            if(json.rooms){
//                                me.rooms = json.rooms;
//                                me.$nextTick(function () {
//                                    me.initJs();
//                                })
//                            }
//                            if(json.message){
//                                bookingCoreApp.showAjaxMessage(json);
//                            }
//                        },
//                        error:function (e) {
//                            //console.log(e);
//                            me.firstLoad = false;
//                            bookingCoreApp.showAjaxError(e);
//                        }
//                    })
                }
                
            },
            removeActivityFromSelected:function(index) {
                this.total_price -= this.html[index].item_price;
                this.total_activities--;
                this.html.splice(index,1);
                this.idsHtml.splice(index,1);
            },
            handleTotalPrice:function() {
            },
            formatMoney: function (m) {
                return window.bravo_format_money(m);
            },
            
            doProceed:function (e) {
                e.preventDefault();
                if(this.onSubmit) return false;

                if(!this.validate()) return false;

                this.onSubmit = true;
                var me = this;

                this.message.content = '';

                if(this.step == 1){
                    this.html = '';
                }

                $.ajax({
                    url:bookingCore.url+'/booking/addToCart',
                    data:{
                        service_id:this.id,
                        service_type:"activity",
                        start_date:this.start_date,
                        end_date:this.end_date,
                        // person_types:this.person_types,
                        extra_price:this.extra_price,
                        total_price:this.total_price,
                        // step:this.step,
                    },
                    dataType:'json',
                    type:'post',
                    success:function(res){

                        if(!res.status){
                            me.onSubmit = false;
                        }
                        if(res.message){
                            bookingCoreApp.showAjaxMessage(res);
                        }

                        if(res.step){
                            me.step = res.step;
                        }
                        if(res.html){
                            me.html = res.html
                        }

                        if(res.url){
                            window.location.href = res.url
                        }

                        if(res.errors && typeof res.errors == 'object')
                        {
                            var html = '';
                            for(var i in res.errors){
                                html += res.errors[i]+'<br>';
                            }
                            me.message.content = html;

                            bookingCoreApp.showError(html);
                        }
                    },
                    error:function (e) {
                        console.log(e);
                        me.onSubmit = false;

                        bravo_handle_error_response(e);

                        if(e.status == 401){
                            //$('.bravo_single_book_wrap').modal('hide');
                        }

                        if(e.status != 401 && e.responseJSON){
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            me.message.type = false;

                        }
                    }
                })
            },
            openStartDate:function(){
                //$(this.$refs.start_date).trigger('click');
            },
        }

    });
});