/**
 * @author 愚人码头
 * User: feiwen
 * Date: 11-7-11
 * Time: 下午4:30
 * Settings:{
 *     speed:滚动速度
 *     timer:滚动的时间间隔
 * }
 */
(function($) {
    $.fn.slideImg = function(settings) {
        settings = jQuery.extend({
            speed: "normal",
            timer: 1000
        }, settings);
        return this.each(function() {
            $.fn.slideImg.scllor($(this), settings);
        });
    };

    $.fn.slideImg.scllor = function($this, settings) {
        var index = 0;
        var scllorer=$(".img-box li",$this);
        var size=scllorer.size();
        var slideH=scllorer.outerHeight();
        var $selItem=$("<ul></ul>",{
            "class":"flash_item",
            html:function(){
                var $selItemHTML="";
                if(size==1){
                    return;
                }else if(size>1){
                    for(var i=0;i<size ;i++){
                        i==0?$selItemHTML+='<li class="on">'+(i+1)+'':$selItemHTML+='<li class="">'+(i+1)+'</li>';
                    }
                    return  $selItemHTML;
                }
            }
        }).appendTo($this);
        var li = $(".flash_item li",$this);
        var showBox = $(".img-box",$this);
        var intervalTime=null;
        li.hover(function() {
            var that=this;
            if (intervalTime) {
                clearInterval(intervalTime);
            }
            intervalTime = setTimeout(function() {
                index = li.index(that);
                ShowAD(index);
            }, 200);
        }, function() {
            clearInterval(intervalTime);
            interval();
        });
        showBox.hover(function() {
            if (intervalTime) {
                clearInterval(intervalTime);
            }
        }, function() {
            clearInterval(intervalTime);
            interval();
        });
        function interval(){
            intervalTime = setInterval(function() {
                ShowAD(index);
                index++;
                if (index == size) {
                    index = 0;
                }
            }, settings.timer);
        }
        interval();
        var ShowAD = function(i) {
            showBox.animate({ "top": -i * slideH }, settings.speed);
            li.eq(i).addClass("on").siblings().removeClass("on");
        };
    };
})(jQuery);