jQuery(function ($) {
    
var activityForm2 = new Vue({
        el:'.bravo_search_activity',
        data:{
            total_price:price,
            total_activities:activities,
            total_hotels:0,
            total_cars:0,
            total_guides:0,
            total_tours:0,
            html:[],
            idsHtml:activities_ids,
        },        
        methods:{
            addItemToSelected:function(event,parentClass,itemType) {
                event.preventDefault();
                //alert($("#"+parentClass).find(".text-price").html().toString())
                console.log(this.html.findIndex(x => x.id == parentClass))
                if(this.html.findIndex(x => x.id == parentClass)>-1)
                {
                    bookingCoreApp.showError("Item already selected");
                    //alert("Item already selected.");
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
                    item_type:itemType,
                    });
                    this.total_price += parseInt($("#"+parentClass).find(".text-price").html().match(/[0-9]+/));
                    this.total_activities++;                    
                    this.idsHtml.push(
                            {
                                service_id:$("#"+parentClass).find(".activity_id").val(),
                                service_type:itemType,
                                service_price:$("#"+parentClass).find(".price_id").val(),
                            }
                            );
                    console.log(this.idsHtml);
                        //this.idsHtml.push('<input type="hidden" value="'+$("#"+parentClass).find(".activity_id").val()+'" name="hotel_ids[]"/>');
                    
                    
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

                if(!this.idsHtml) return false;

                this.onSubmit = true;
                var me = this;
                if(this.step == 1){
                    this.html = '';
                }

                $.ajax({
                    url:bookingCore.url+'/booking/addToCartMany',
                    data:{
                        data:this.idsHtml,
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
                            //me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            //me.message.type = false;

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