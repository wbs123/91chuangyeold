$(function(){
	//显示网站导航
	$('#topNavRightNav').on('mouseenter', function() {
		$('#netNav').stop(false, true);
		$('#netNav').slideDown();
	});
	$('.h-top').on('mouseleave', function() {
		$('#netNav').stop(false, true);
		$('#netNav').slideUp();
	});
	
	/*hdxw-news-tabs*/
	$(".hdxw .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".hdxw .zixun .cont").eq(0).show().siblings().hide();
	$(".hdxw .tabs li").hover(function() {
		$(".hdxw .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".hdxw .zixun .cont").hide().eq($(this).index()).show();
	});
	
	/*hdxw-news-tabs*/
	$(".phb-top .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".phb-top .zixun .cont").eq(0).show().siblings().hide();
	$(".phb-top .tabs li").hover(function() {
		$(".phb-top .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".phb-top .zixun .cont").hide().eq($(this).index()).show();
	});
	
	/*项目推荐*/
	$(".project-tj .tabs a").eq(0).addClass("on").siblings().removeClass('on');
  $(".project-tj .small ul").eq(0).show().siblings().hide();   
	$(".project-tj .tabs a").hover(function() {
		$(".project-tj .tabs a").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".project-tj .small ul").hide().eq($(this).index()).show();
	});
	
	$(".project-news .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".project-news .cont").eq(0).show().siblings().hide();   
	$(".project-news .tabs li").hover(function() {
		$(".project-news .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".project-news .cont").hide().eq($(this).index()).show();
	});
	
	/*项目库-新闻资讯*/
	$(".side-news .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".side-news .cont ul").eq(0).show().siblings().hide();   
	$(".side-news .tabs li").hover(function() {
		$(".side-news .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".side-news .cont ul").hide().eq($(this).index()).show();
	});
	
	/*项目库-行业推荐*/
	$(".hangye-tj .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".hangye-tj .cont ul").eq(0).show().siblings().hide();   
	$(".hangye-tj .tabs li").hover(function() {
		$(".hangye-tj .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".hangye-tj .cont ul").hide().eq($(this).index()).show();
	});
	
	
	/*phb-pages*/
	$(".phb-item").each(function(){
		$(this).find(".cont li").first().addClass("link-hover");
	});
	$(".phb-item .cont li").hover(function() {
		$(this).addClass("link-hover");
		$(this).siblings().removeClass("link-hover");
	});
});

/*banner*/
$(function(){
    //slideshow
    var el = null;
    var elSize = {
        width: 0,
        height: 0
    };
    var idx = 0;
    var length = 0;
    var timerId = null;
    //动画方向：
    //POS_LTR = 从左到右
    //POS_TTB = 从上到下
    var POS_LTR = 0, POS_TTB = 1;
    var position = POS_LTR;
    
    function slideTo(toIdx){
        idx = toIdx;
        if (position == POS_LTR) {
            el.find(".hdxw-img .images-inner").animate({
                left: (-elSize.width * idx) + "px"
            });
        } else if (position == POS_TTB) {
            el.find(".hdxw-img .images-inner").animate({
                top: (-elSize.height * idx) + "px"
            });
        }
        el.find(".hdxw-dots .dots-inner .dot-icon").eq(idx).addClass("dot-active").removeClass("dot-normal").siblings().addClass("dot-normal").removeClass("dot-active");
        startTimer();
    }
    
    function stopTimer(){
        if (timerId) {
            window.clearTimeout(timerId);
            timerId = null;
        }
    }
    
    function startTimer(){
        stopTimer();
        timerId = window.setTimeout(function(){
            var toIdx = idx + 1;
            if (toIdx > length - 1) {
                toIdx = 0;
            }
            slideTo(toIdx);
        }, 5000);
    }
    
    function initDotsHtml(){
        var html = '';
        for (var i = 0; i < length; i++) {
            html += '<a class="dot-icon ' + (i === 0 ? 'dot-active' : 'dot-normal') + '" data-idx="' + i + '">&nbsp;&nbsp;&nbsp;</a>';
        }
        el.find(".hdxw-dots .dots-inner").html(html);
        
        el.find(".hdxw-dots .dots-inner .dot-icon").click(function(){
            var toIdx = parseFloat($(this).attr("data-idx"));
            slideTo(toIdx);
        });
    }
    
    function hdxw(){
        el = $(".hdxw");
        elSize.width = el.width();
        elSize.height = el.height();
        length = el.find(".hdxw-img .images-inner .link").length;
        $(".hdxw-img .images-inner .link").width(elSize.width);
		
        if (position == POS_LTR) {
            el.find(".hdxw-img .images-inner").width(elSize.width * length).height(elSize.height);
        } else if (position == POS_TTB) {
            el.find(".hdxw-img .images-inner").width(elSize.width).height(elSize.height * length);
        }
        
        initDotsHtml();
        
        startTimer();
    }
    
    hdxw();
    
});



//图片懒加载
$(function() {
  $("img.lazy").lazyload({
			effect: "fadeIn",
			threshold: 200,
			skip_invisible: false
	});
});