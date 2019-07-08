/*项目轮播*/
$(function(){
	$(".bigimgbox img").attr("src",$(".smallimgbox ul li").eq(0).find("img").attr("src"));
	$(".smallimgbox ul li").eq(0).addClass("on");
	$(".smallimgbox ul li").on("click",function(){
		console.info($(this).index())
		changeimg($(this))
		btnindex=$(".smallimgbox ul li.on").index();
	})
	function changeimg(obj){
		obj.addClass("on").siblings().removeClass("on")
		var index=obj.index();
		var smallsrc=obj.find("img").attr("src");
		obj.closest(".xm-pic").find(".bigimgbox img").attr("src",smallsrc);
	}
	var btnindex=0;
	var marginLeft=0;
	var before,after;
	$(".prevbtn").on("click",function(){
		changepos('left')
		before=$(".smallimgbox ul li").last().remove();
		$(".smallimgbox ul li").first().before(before);
	})
	$(".nextbtn").on("click",function(){
		changepos('right')
		after=$(".smallimgbox ul li").first().remove();
		$(".smallimgbox ul li").last().after(after);
	})
	function changepos(string){
		btnindex=$(".smallimgbox ul li.on").index()
		switch(string){
			case 'left':
				btnindex--;
				if(btnindex==-1){ btnindex=0; }
				break;
			case 'right':
				btnindex++;
				if(btnindex==$(".smallimgbox ul li").length){
					btnindex=$(".smallimgbox ul li").length-1;
				}
				break;
		}
		$(".smallimgbox ul li").eq(btnindex).addClass("on").siblings().removeClass("on")
		var src=$(".smallimgbox ul li.on img").attr("src")
		$(".xm-pic .bigimgbox img").attr("src",src)
	}
	
	/*img有无缩略图做判断*/
	var bigImg = $("#item2 .bigimgbox");
	var smallImg = $("#item2 .smallimgbox .midbox li");
	var findsmallImg = $("#item2 .smallimgbox .midbox ul");
	if(smallImg.length == 1 && smallImg.find("img").length==0){
		smallImg.append('<img src="http://www.91chuangye.com/images/defaultpic.gif">');
		bigImg.append('<img src="http://www.91chuangye.com/images/defaultpic.gif">');
	}
	
	if(findsmallImg.find("li").length == 0){
		findsmallImg.append('<li class="on"><img src="http://www.91chuangye.com/images/defaultpic.gif"></li>');
		bigImg.find("img").attr("src","http://www.91chuangye.com/images/defaultpic.gif");
	}
	
	$("#item2 .smallimgbox .midbox li").each(function(){
		if($(this).find("img").length==0){
			$(this).hide()
		}
	});
})

$(function() {
	//显示网站导航
	$('#topNavRightNav').on('mouseenter', function() {
		$('#netNav').stop(false, true);
		$('#netNav').slideDown();
	});
	$('.h-top').on('mouseleave', function() {
		$('#netNav').stop(false, true);
		$('#netNav').slideUp();
	});
	
	/*热门品牌-品牌排行*/
	$(".side-brandphb .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".side-brandphb .tabs-cont .cont").eq(0).show().siblings().hide();   
	$(".side-brandphb .tabs li").hover(function() {
		$(".side-brandphb .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".side-brandphb .tabs-cont .cont").hide().eq($(this).index()).show();
	});
	
	/*新闻资讯*/
	$(".side-news .tabs li").eq(0).addClass("on").siblings().removeClass('on');
	$(".side-news .cont ul").eq(0).show().siblings().hide();   
	$(".side-news .tabs li").hover(function() {
		$(".side-news .tabs li").eq($(this).index()).addClass("on").siblings().removeClass('on');
		$(".side-news .cont ul").hide().eq($(this).index()).show();
	});
	
	/*排行榜*/
	$(".side-brandphb .cont2 dl").eq(0).addClass("on").siblings().removeClass('on')
	$(".side-brandphb .cont2 dl").hover(function() {
		$(this).addClass("on");
		$(this).siblings().removeClass("on");
	});
	
})

/*平滑滚动*/
 $(function () {
	$('.nav-jmxq,.nav-jmfy,.nav-jmys,.nav-jmlc').click(function () {
		if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
			var $target = $(this.hash);
			$target = $target.length && $target || $('[name=' + this.hash.slice(1) + ']');
			if ($target.length) {
				var targetOffset = $target.offset().top;
				$('html,body').animate({
						scrollTop: targetOffset
					},
					1000);
				return false;
			}
		}
	});
})

$(function(){
    $('a[href*=#]').click(function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
        && location.hostname == this.hostname) {
            var $target = $(this.hash);
            $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
            if ($target.length) {
                var targetOffset = $target.offset().top;
                $('html,body').animate({scrollTop: targetOffset}, 1000);
                return false;
            }
        }
    });
});

//图片懒加载
$(function() {
  $("img.lazy").lazyload({
		effect: "fadeIn",
		threshold: 200,
		skip_invisible: false
	});
});