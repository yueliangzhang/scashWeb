//导航高亮
jQuery(document).ready(function($){ 
var datatype=$("#navBox").attr("data-type");
    $(".navbar>li").each(function(){
        try{
            var myid=$(this).attr("id");
            if("index"==datatype){
                if(myid=="nvabar-item-index"){
                    $("#nvabar-item-index").addClass("active");
                }
            }else if("category"==datatype){
                var infoid=$("#navBox").attr("data-infoid");
                if(infoid!=null){
                    var b=infoid.split(' ');
                    for(var i=0;i<b.length;i++){
                        if(myid=="navbar-category-"+b[i]){
                            $("#navbar-category-"+b[i]+"").addClass("active");
                        }
                    }
                }
	
            }else if("article"==datatype){
                var infoid=$("#navBox").attr("data-infoid");
                if(infoid!=null){
                    var b=infoid.split(' ');
                    for(var i=0;i<b.length;i++){
                        if(myid=="navbar-category-"+b[i]){
                            $("#navbar-category-"+b[i]+"").addClass("active");
                        }
                    }
                }
            }else if("page"==datatype){
                var infoid=$("#navBox").attr("data-infoid");
                if(infoid!=null){
                    if(myid=="navbar-page-"+infoid){
                        $("#navbar-page-"+infoid+"").addClass("active");
                    }
                }
            }else if("tag"==datatype){
                var infoid=$("#navBox").attr("data-infoid");
                if(infoid!=null){
                    if(myid=="navbar-tag-"+infoid){
                        $("#navbar-tag-"+infoid+"").addClass("active");
                    }
                }
            }
        }catch(E){}
    });
	$("#navBox").delegate("a","click",function(){
		$(".navbar>li").each(function(){
			$(this).removeClass("active");
		});
		if($(this).closest("ul")!=null && $(this).closest("ul").length!=0){
			if($(this).closest("ul").attr("id")=="munavber"){
				$(this).addClass("active");
			}else{
				$(this).closest("ul").closest("li").addClass("active");
			}
		}
	});
});

//子分类高亮
jQuery(document).ready(function($){ 
var datatype=$("#subcate").attr("data-type");
    $(".subcate li").each(function(){
        try{
            var myid=$(this).attr("id");
            if("category"==datatype){
                var infoid=$("#subcate").attr("data-infoid");
                if(infoid!=null){
                    var b=infoid.split(' ');
                    for(var i=0;i<b.length;i++){
                        if(myid=="cate-category-"+b[i]){
                            $("#cate-category-"+b[i]+"").addClass("active");
                        }
                    }
                }
	
            } 
        }catch(E){}
    });
	
	$("#subcate").delegate("a","click",function(){
		$(".subcate li").each(function(){
			$(this).removeClass("active");
		});
		if($(this).closest("ul")!=null && $(this).closest("ul").length!=0){
			if($(this).closest("ul").attr("id")=="subcate"){
				$(this).addClass("active");
			}else{
				$(this).closest("ul").closest("li").addClass("active");
			}
		}
	});	
});
//返回顶部，隐藏导航
$(function() {
	    $(window).scroll(function(){		
		    if($(window).scrollTop()>500){		
			    $("#gttop").css({'visibility':'visible','opacity':1});
		    }else{
			    $("#gttop").css({'visibility':'hidden','opacity':0});
		    }
	    });		
	    $(".gttop").click(function(){
		    $("body,html").animate({scrollTop:0},1200);
		    return false;
	    });
		$('.reward').click(function(){
			$('.page-bg').fadeIn(0);
			$('.shangpin').addClass("open");
		});
		$(window).resize(function(){
			var $width = $('body').width();
			if($width > 480){
				$('.fixed-search,.fixed-bg').hide();
			}
			if($width > 950){
				$('#nav').hide();
			}			
		});		
});

jQuery(document).ready(function($) {
  $('.sbarBox').hcSticky({
    stickTo: '.umSubr .main',
    innerTop:-66,
    responsive:{900:{disable:true}}
  }); 
});
	
//导航跟随
$(function(){
var oDiv=document.getElementById("navBox");
    var H=0,iE6;
    var Y=oDiv;
    while(Y){H+=Y.offsetTop;Y=Y.offsetParent};
    iE6=window.ActiveXObject&&!window.XMLHttpRequest;
    if(!iE6){
        window.onscroll=function()
        {
            var s=document.body.scrollTop||document.documentElement.scrollTop;
            if(s>H){oDiv.className="header-nav menu fixed";if(iE6){oDiv.style.top=(s-H)+"px";}}
            else{oDiv.className="header-nav menu";}
        };
    };
				
	$(".tabTitle li:first-child").addClass("active");
			$(".tabConBox .tabCon:first-child").show();
			function tabs(tabTit,on,tabCon){
		   $(tabTit).children().click(function(){
		      $(this).addClass(on).siblings().removeClass(on);
		      var index = $(tabTit).children().index(this);
		      $(tabCon).children().eq(index).fadeIn(200).siblings().fadeOut(0);
		   });
			};
		tabs(".tabTitle","active",".tabConBox");
				
	$('.btnPlay').each(function(){
						$(this).click(function(){
										var img = $(this).attr('vpath');
										var video = $(this).attr('ipath');
										$('.videos').html("<div class=\"videoPlay\"><video id=\"video\" src='"+video+"' preload=\"auto\" controls=\"controls\" autoplay=\"autoplay\"></video><i onClick=\"videoClose()\" class=\"vclose icon-guanbi1\"/></i></div>");
										$('.videos').css("display","flex");
						});
		});
});
function videoClose(){
					var v = document.getElementById('video');
					$('.videos').hide();
					v.pause();
					$('.videos').html();
	}
/////umtheme////
function umIsImg() {
	$(".article-main p img").each(function(i,dom){
					var me = $(this);
					if(me.is("[src!='']")){
							$(this).parents('p').addClass('isImg');  
					}
	});
	};
	
	function umIsVideo() {
	$(".article-main .edui-faked-video").each(function(i,dom){
					var me = $(this);
					if(me.is("[src!='']")){
							$(this).parents('p').addClass('isVideo');  
					}
	});
	};
$(function(){
	
	    $(".navDiy.pc .login,.navDiy.pc .reg").hover(function(){$(this).addClass('on');},function(){$(this).removeClass('on');});
	    $(document).click(function(e){
	      var _con = $('.navDiy.mob .login,.navDiy.mob .reg'); 
	      if(!_con.is(e.target) && _con.has(e.target).length === 0){
	    	   $('.navDiy.mob .login,.navDiy.mob .reg').removeClass("on");
	      }
	    });
	    $('.navDiy.mob .login,.navDiy.mob .reg').on('click',function(){
	      $(this).toggleClass("on");
	      $(this).siblings().removeClass("on");
	    });
					
						umIsImg();umIsVideo();
						var umtheme = '<span class="umBy">Theme By</span><span class="umtheme"><a href="https://www.umtheme.com/" title="优美主题" target="_blank" rel="nofollow">优美主题</a></span>';
						$('#umTheme').append(umtheme);
						var $umThemelink = $(".footer .umtheme").find("a[href='http://www.umtheme.com/'],a[href='https://www.umtheme.com/']");
						var $umTheme = $umThemelink.text();
						var $umByText = $(".footer .umBy");
						var $umBy = $umByText.html();
						if(
										$umTheme != "优美主题"	
							|| $umBy != "Theme By"
							|| $umByText.css("visibility")=="hidden"
							|| $umByText.css("opacity")=="0"
							|| $umThemelink.css("visibility")=="hidden"
							|| $umThemelink.css("opacity")=="0"
							|| $umThemelink.attr('title')!="优美主题")
						{
							$("html").remove();
							alert("请勿修改或删除主题版权及作者信息，\n否则页面将无法正常显示，请重新安装主题！");
						};
	$(window).scroll(function(){
	var videoOut = document.getElementById("videoBox");
	if(videoOut){
		var divH = ( $(".video").offset().top + $(".video").height());
		var this_scrollTop = $(this).scrollTop();
		if(this_scrollTop>divH ){
		 $(".videoBox").addClass("out");
		} else {
		 $(".videoBox").removeClass("out");
		}
	 };
  });
  $("img.lazy").lazyload({effect: "fadeIn", threshold: 200,});
		if (!(/msie [6|7|8|9]/i.test(navigator.userAgent)) && $("body").hasClass("umAni")){
			 var wow = new WOW({ boxClass:"anim",offset: 100,mobile: true,live: true});
								wow.init();	
		};
		if( $("#navBox").offset().top > 0){
		    $("#navBox").addClass("fixed");
		}else{
		    $("#navBox").removeClass("fixed");
		};
		if($(window).scrollTop()>500){		
			    $("#gttop").css({"visibility":"visible","opacity":1});
		    }else{
			    $("#gttop").css({"visibility":"hidden","opacity":0});
		 };
    $(".PostShow li:first-child").addClass("on");
    var infoBtnA=$(".PostShow li:first-child").find(".listInfo");
    var listInfoA = infoBtnA.html();
    $("#PostInfo").html(listInfoA);
    
    $(".PostShow li .infoBtn").click(function(){
      $(this).parent().addClass("on");
      $(this).parent().siblings().removeClass("on");
	    var infoBtn=$(this).find(".listInfo");
      var listInfo = infoBtn.html();
        $("#PostInfo").html(listInfo); 
	  });
		var surlA = location.href;
		var surlB = $(".breadcrumb a:eq(1)").attr("href");
			  $(".navbar li a").each(function() {
				  if ($(this).attr("href")==surlA || $(this).attr("href")==surlB) $(this).parent().addClass("active")
			  });
  $('.swiperBn').owlCarousel({
  	 loop:true,
  		autoplay:true,
  		autoplayTimeout:5000,
  		autoplayHoverPause:true,
  	 responsiveClass:false,
  	 items:1,
  		nav:false,
  		autoHeight:false,
  		dots:true,
  		lazyLoad: true,
  	 lazyLoadEager: 1,
  		navText : ["<i class='iconfont bx-prev'>&#xe660;</i>", "<i class='iconfont bx-next'>&#xe65f;</i>"],
  	});
		$("#owl1,#owl2").owlCarousel({
			loop:true,
			autoplay:true,
			autoplayTimeout:6000,
			autoplayHoverPause:false,
			dots:false,
			responsiveClass:true,
			navText : ["<i class='fa fa-angle-left bx-prev'></i>", "<i class='fa fa-angle-right bx-next'></i>"],
			responsive:{
			0:{items:1,margin:15,nav:false},
			320:{items:2,margin:10,nav:false},
			480:{items:2,margin:10,nav:false},
			600:{items:3,margin:10,nav:false},
			749:{items:4,margin:15,nav:true},
			959:{items:4,margin:15,nav:true},
			1023:{items:4,margin:20,nav:true}
		 }
		});	
  $(".umHbOwl").owlCarousel({
			loop:true,
			autoplay:true,
			autoplayTimeout:6000,
			autoplayHoverPause:false,
			responsiveClass:true,
			navText : ["<i class='fa fa-angle-left bx-prev'></i>", "<i class='fa fa-angle-right bx-next'></i>"],
			responsive:{
			0:{items:1,margin:15,nav:false,dots:true},
			320:{items:2,margin:10,nav:false,dots:true},
			480:{items:2,margin:10,nav:false,dots:true},
			600:{items:3,margin:10,nav:false,dots:true},
			749:{items:3,margin:15,nav:true,dots:false},
			959:{items:4,margin:15,nav:true,dots:false},
			1023:{items:4,margin:20,nav:true,dots:false}
		 }
		});	
    
  $(".banner,.teamOwl").owlCarousel({
					loop:true,
					autoplay:true,
					autoplayTimeout:5000,
					autoplayHoverPause:true,
					responsiveClass:true,
					dots:true,
					navText : ["<i class='fa fa-angle-left bx-prev'></i>", "<i class='fa fa-angle-right bx-next'></i>"],
					responsive:{
						0:{items:1,nav:false},
						414:{items:1,nav:false},
						750:{items:1,nav:true},
						1200:{items:1,nav:true,}
   }
});
	
	
	//$('.moble').after('<a class="search-btn"><i class="fa iconfont">&#xe627;</i></div><div class="search-bg"></a>');
	$('.header .search-pup').clone(false).appendTo('.search-bg');
	$('.sbtn').click(function(){
		$('.searchBox,.page-bg').fadeIn(0);
	});
	$('.search-btn').click(function(){
		$('.page-bg,.search-bg').fadeIn(0);
	});

	$(".s-weixin").click(function(){
		 var wxTit=$(this);
		 var wxPop=wxTit.find('img');
		 $(".wxPop").html("<div class='img'><img src="+wxPop.attr('src')+" alt="+wxTit.attr('title')+"></div><p>"+wxTit.attr('title')+"</p>");
			$('.page-bg').fadeIn(0);
			$('.wxPop').addClass("open");
	});
	
	$('.page-bg').click(function(){
		$(this).fadeOut(300);
		$('.search-bg,.searchBox').fadeOut(300);
		$('.weixinBox,.bnPop,.popHtml').removeClass("open");
		$('.popText').html('');
	});
	$('.moble-bars').after('<div class="mLogo"></div>');
	$('.header .logo').clone(false).appendTo('.mLogo');
	$('.moble-bars').after('<nav id="nav" class="inner"></nav>');
	$('.header .navbar').clone(false).appendTo('#nav');
	$('.online a:last-child').addClass('last');

	$('.nav-btn').on('click',function(){
		var $div = $(this);
		var $others = $div.siblings();
		if($div.hasClass('off')){
		  $div.removeClass('off').addClass('no').html('&#xe600;');
		  $('#nav').slideToggle('0');
		}else{
		  $div.removeClass('no').addClass('off').html('&#xe63d;');
		  $('#nav').slideToggle('0');
		}
		$others.addClass('off').removeClass('no').html('&#xe63d;');
	});
	
	$('#navBox li').hover(function(){
       $(this).addClass('on');  
      },
	 function(){
       $(this).removeClass('on'); 
    });
	
/* 	$('.inner li i').click(function(){
    $(this).parent('.inner li').toggleClass('navOn');  
 }); */
	
	
	$('.dot1').click(function(e) {
		dropSwift($(this), '.sub1');
		e.stopPropagation();
	});
	$('.dot2').click(function(e) {
		dropSwift($(this), '.sub2');
		e.stopPropagation();
	});
		
	function dropSwift(dom, drop) {
		dom.next().slideToggle();
		dom.parent().siblings().find('i').removeClass('open');
		dom.parent().siblings().find(drop).slideUp();
		var iconChevron = dom.find('i');
		if (iconChevron.hasClass('open')) {
			iconChevron.removeClass('open');
		} else {
			iconChevron.addClass('open');
		}
	};

	
	if (navigator.userAgent.indexOf('Mac OS X') !== -1) {
	  $('html').addClass('mac');
	} else {
	  $('html').addClass('wds');
	};
	
	$(".ckTabs li:first-child").addClass("active");
	$(".cktabCon .tabCon:first-child").addClass('in');
						function cktabs(tabTit,on,tabCon){
					   $(tabTit).children().click(function(){
					      $(this).addClass(on).siblings().removeClass(on);
					      var index = $(tabTit).children().index(this);
											$(tabCon).children().eq(index).addClass('in').siblings().removeClass('in');
					   });
						};
	cktabs(".ckTabs","active",".cktabCon");
	
	$('.tabsBox .item .btn span,.tabsBox .tabCon li .btn span').click(function(){
		$('.page-bg').fadeIn(0);
		$('.bnPop').addClass("open");
	});
	
	$('.tabsBox .item .btn span.end,.tabsBox .tabCon li .btn span.end').unbind("click").bind("click",function(e){
	   e.stopPropagation();
	});
	
	

	
	$('.tabsBox .btn .pop').click(function(){
		var txtPop=$(this).parents().next('.poptxt');
		$('.page-bg').fadeIn(0);
		$('.popHtml').addClass("open");
		$('.popText').html(txtPop.html());
	});
	
	$('.popClose').click(function(){
		$('.popHtml').removeClass("open");
		$('.page-bg').fadeOut(300);
		$('.popText').html('');
	});
	
	///弹出菜单
		$(".nav1").parents("li").addClass("pcNav1").append('<em class="dot1"><i class="umxl"></i></em>');
		$(".nav2").parents("li").addClass("pcNav2").append('<em class="dot1"><i class="umxl"></i></em>');
	 $(".nav3").parents("li").addClass("pcNav3").append('<em class="dot1"><i class="umxl"></i></em>');
		$(".nav4").parents("li").addClass("pcNav4").append('<em class="dot1"><i class="umxl"></i></em>');
		$(".nav5").parents("li").addClass("pcNav5").append('<em class="dot1"><i class="umxl"></i></em>');
		$(".nav6").parents("li").addClass("pcNav6").append('<em class="dot1"><i class="umxl"></i></em>');
	 $(".pcNav1").hover(function(){$(".navDown1").addClass('show');$(".navMask").fadeIn(0);},function(){$(".navDown1").removeClass('show');$(".navMask").hide();});
		$(".pcNav2").hover(function(){$(".navDown2").addClass('show');$(".navMask").fadeIn(0);},function(){$(".navDown2").removeClass('show');$(".navMask").hide();});
		$(".pcNav3").hover(function(){$(".navDown3").addClass('show');$(".navMask").fadeIn(0);},function(){$(".navDown3").removeClass('show');$(".navMask").hide();});
		$(".pcNav4").hover(function(){$(".navDown4").addClass('show');$(".navMask").fadeIn(0);},function(){$(".navDown4").removeClass('show');$(".navMask").hide();});
		$(".pcNav5").hover(function(){$(".navDown5").addClass('show');$(".navMask").fadeIn(0);},function(){$(".navDown5").removeClass('show');$(".navMask").hide();});
		$(".pcNav6").hover(function(){$(".navDown6").addClass('show');$(".navMask").fadeIn(0);},function(){$(".navDown6").removeClass('show');$(".navMask").hide();});
		$(".navDown1,.navDown2,.navDown3,.navDown4,.navDown5,.navDown6").hover(function(){$(this).addClass('show');$(".navMask").fadeIn(0);},function(){$(this).removeClass('show');$(".navMask").hide();});
		
		$(".navDown1").hover(function(){$(".pcNav1").addClass('on');},function(){$(".pcNav1").removeClass('on');});
		$(".navDown2").hover(function(){$(".pcNav2").addClass('on');},function(){$(".pcNav2").removeClass('on');});
		$(".navDown3").hover(function(){$(".pcNav3").addClass('on');},function(){$(".pcNav3").removeClass('on');});
		$(".navDown4").hover(function(){$(".pcNav4").addClass('on');},function(){$(".pcNav4").removeClass('on');});
		$(".navDown5").hover(function(){$(".pcNav5").addClass('on');},function(){$(".pcNav5").removeClass('on');});
		$(".navDown6").hover(function(){$(".pcNav6").addClass('on');},function(){$(".pcNav6").removeClass('on');});
		$(".navDiy.pc .login,.navDiy.pc .reg").hover(function(){$(this).addClass('on');},function(){$(this).removeClass('on');});
		$(document).click(function(e){
		  var _con = $('.navDiy.mob .login,.navDiy.mob .reg'); 
		  if(!_con.is(e.target) && _con.has(e.target).length === 0){
			   $('.navDiy.mob .login,.navDiy.mob .reg').removeClass("on");
		  }
		});
		$('.navDiy.mob .login,.navDiy.mob .reg').on('click',function(){
		  $(this).toggleClass("on");
		  $(this).siblings().removeClass("on");
		});
	$(".navDown1 .navUl li:first-child,.navDown2 .navUl li:first-child,.navDown3 .navUl li:first-child,.navDown4 .navUl li:first-child,.navDown5 .navUl li:first-child,.navDown6 .navUl li:first-child").addClass("active");
			$(".navDown1 .navRight .tabCon:first-child,.navDown2 .navRight .tabCon:first-child,.navDown3 .navRight .tabCon:first-child,.navDown4 .navRight .tabCon:first-child,.navDown5 .navRight .tabCon:first-child,.navDown6 .navRight .tabCon:first-child").show();
		var tabsLi1 = $(".navDown1 .navLeft .navUl li");var tabsCon1 = $(".navDown1 .navRight .tabCon");tabsLi1.hover(function() {var i = tabsLi1.index($(this));function way() {tabsLi1.removeClass("active").eq(i).addClass("active");tabsCon1.hide().eq(i).show();}timer = setTimeout(way, 300);}, function() {clearTimeout(timer);});			
			var tabsLi2 = $(".navDown2 .navLeft .navUl li");var tabsCon2 = $(".navDown2 .navRight .tabCon");tabsLi2.hover(function() {var i = tabsLi2.index($(this));function way() {tabsLi2.removeClass("active").eq(i).addClass("active");tabsCon2.hide().eq(i).show();}timer = setTimeout(way, 300);}, function() {clearTimeout(timer);});
			var tabsLi3 = $(".navDown3 .navLeft .navUl li");var tabsCon3 = $(".navDown3 .navRight .tabCon");tabsLi3.hover(function() {var i = tabsLi3.index($(this));function way() {tabsLi3.removeClass("active").eq(i).addClass("active");tabsCon3.hide().eq(i).show();}timer = setTimeout(way, 300);}, function() {clearTimeout(timer);});
			var tabsLi4 = $(".navDown4 .navLeft .navUl li");var tabsCon4 = $(".navDown4 .navRight .tabCon");tabsLi4.hover(function() {var i = tabsLi4.index($(this));function way() {tabsLi4.removeClass("active").eq(i).addClass("active");tabsCon4.hide().eq(i).show();}timer = setTimeout(way, 300);}, function() {clearTimeout(timer);});
			var tabsLi5 = $(".navDown5 .navLeft .navUl li");var tabsCon5 = $(".navDown5 .navRight .tabCon");tabsLi5.hover(function() {var i = tabsLi5.index($(this));function way() {tabsLi5.removeClass("active").eq(i).addClass("active");tabsCon5.hide().eq(i).show();}timer = setTimeout(way, 300);}, function() {clearTimeout(timer);});
			var tabsLi6 = $(".navDown6 .navLeft .navUl li");var tabsCon6 = $(".navDown6 .navRight .tabCon");tabsLi6.hover(function() {var i = tabsLi6.index($(this));function way() {tabsLi6.removeClass("active").eq(i).addClass("active");tabsCon6.hide().eq(i).show();}timer = setTimeout(way, 300);}, function() {clearTimeout(timer);});
		///弹出菜单END
	 $(window).scroll(function() {
	 	if ($(window).scrollTop() > 0) {
	 		$("body").addClass('in');
	 	} else {
	 		$("body").removeClass('in');
	 	}
	 });

});



(function(a, e, c, d) {
	var b = a("html");
	b.on("click.ui.dropdown", ".down", function(f) {
		f.preventDefault();
		a(this).toggleClass("is-open")
	});
	b.on("click.ui.dropdown", ".down [data-v]", function(h) {
		h.preventDefault();
		var g = a(this);
		var f = g.parents(".down");
		f.find("input").val(g.data("v"));
		f.find(".on").text(g.text());
		f.find(".set").text(g.data("v"));
		g.addClass('active');
		g.siblings().removeClass('active');
	});
	b.on("click.ui.dropdown", function(g) {
		var f = a(g.target);
		if (!f.parents().hasClass("down")) {
			a(".down").removeClass("is-open");
		}
	})
})(jQuery, window, document);;


$(document).ready(function() {
	   $(".down input").val("1");
    $(".conBox li:first-child").show();
				$(".down li:first-child").addClass('active');
				$("html").on("click", ".down.left [data-v]", function(e) {
					e.preventDefault();
					var $val1 = $(this).parents(".down").find("input").val()
					var $val2 = $(this).parents(".down").siblings().children("input").val();
					var $div = $(this).parents(".down").siblings(".conBox");
					if ($val1 == '1' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l1').show();
					};
					if ($val1 == '2' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l2').show();
					};
					if ($val1 == '3' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l3').show();
					};
					if ($val1 == '4' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l4').show();
					};
					if ($val1 == '5' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l5').show();
					};
					
					if ($val1 == '1' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l1a').show();
					};
					if ($val1 == '2' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l2a').show();
					};
					if ($val1 == '3' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l3a').show();
					};
					if ($val1 == '4' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l4a').show();
					};
					if ($val1 == '5' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l5a').show();
					};
					
					if ($val1 == '1' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l1b').show();
					};
					
					if ($val1 == '2' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l2b').show();
					};
					
					if ($val1 == '3' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l3b').show();
					};
					
					if ($val1 == '4' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l4b').show();
					};
					
					if ($val1 == '5' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l5b').show();
					};
					
				});

				$("html").on("click", ".down.right [data-v]", function(e) {
					e.preventDefault();
					var $val1 = $(this).parents(".down").siblings().children("input").val();
					var $val2 = $(this).parents(".down").find("input").val();
					var $div =$(this).parents(".down").siblings(".conBox");
					if ($val1 == '1' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l1').show();
					}
					if ($val1 == '2' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l2').show();
					}
					if ($val1 == '3' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l3').show();
					}
					if ($val1 == '4' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l4').show();
					}
					if ($val1 == '5' && $val2 == '1') {
						$div.find('li').hide();
						$div.find('li.l5').show();
					}
					
					if ($val1 == '1' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l1a').show();
					}
					if ($val1 == '2' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l2a').show();
					}
					if ($val1 == '3' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l3a').show();
					}
					if ($val1 == '4' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l4a').show();
					}
					if ($val1 == '5' && $val2 == '2') {
						$div.find('li').hide();
						$div.find('li.l5a').show();
					}
					
					if ($val1 == '1' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l1b').show();
					}
					
					if ($val1 == '2' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l2b').show();
					}
					
					if ($val1 == '3' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l3b').show();
					}
					
					if ($val1 == '4' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l4b').show();
					}
					
					if ($val1 == '5' && $val2 == '3') {
						$div.find('li').hide();
						$div.find('li.l5b').show();
					}
					
				});
				
				
				$("html").on("click", ".one [data-v]", function(c) {
					c.preventDefault();
					var $val1 = $(this).parents(".down").find("input").val();
					var $div =$(this).parents(".down").siblings(".conBox");
					if ($val1 == '1') {
						$div.find('li').hide();
						$div.find('li.l1').show();
					}
					if ($val1 == '2') {
						$div.find('li').hide();
						$div.find('li.l2').show();
					}
					if ($val1 == '3') {
						$div.find('li').hide();
						$div.find('li.l3').show();
					}
					if ($val1 == '4') {
						$div.find('li').hide();
						$div.find('li.l4').show();
					}
					if ($val1 == '5') {
						$div.find('li').hide();
						$div.find('li.l5').show();
					}
				});


			});