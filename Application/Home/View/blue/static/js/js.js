// JavaScript Document
 $(function(){
	//文本框开始
	   //获得焦点
	$("#txt").focus(function(){
		var txt=$(this).val();
		if(txt=="6520FZ0514"){
			$(this).val("");}
		})
		//失去焦点
		$("#txt").blur(function(){
			var txt=$(this).val();
			if(txt==""){
				$(this).val("6520FZ0514");}
			
			})   
			//文本框开始
	   //获得焦点
	$("#wenben").focus(function(){
		var wenben=$(this).val();
		if(wenben=="关键词搜索"){
			$(this).val("");}
		})
		//失去焦点
		$("#wenben").blur(function(){
			var wenben=$(this).val();
			if(wenben==""){
				$(this).val("关键词搜索");}
			
			})  
	/*轮播图*/
	//隐藏第一张以外的图片
	$('.box2>ul>li:gt(0)').hide();
	var n=0;
	var len=$('.box2>ul>li').length; //获取li的个数
	var t;
	function play(){
		//alert(n)
		$('.box2 ul li').eq(n).show().siblings().hide();
		//给当前a增加on样式 兄弟的a移除样式on
		$('#a a').eq(n).addClass('on1').siblings().removeClass('on1');
	}
	function autoPlay(){
		//图片自动轮播
		t=setInterval(function(){
			//alert(1)	
			n++;
			if(n>=len){
				n=0;
			}
			play();
		},3000)	
	}
	//alert(len)
	autoPlay();
	//鼠标放到banner上停止播放，移开继续播放
	$('.box2').hover(function(){
		clearInterval(t);
	},function(){
		autoPlay();
	})
	//点击数字显示相应的图片
	$('#a a').each(function(index) {
        //alert(index)
		$(this).click(function(){
			//alert(index)	
			//alert(n)
			n=index;
			play();
		})
    });
	/*左右滑动*/
	//动态设置ul的宽度
	var leng=$('.GD P').length; //li的个数
	//alert(leng)
	var ulW=388*leng; //ul的宽度值
	var i=0; //i代表移动840的倍数
	//alert(ulW)
	//设置ul的宽度
	$('.GD').css({'width':ulW+'px'});//变量不能放在引号里面 此处的加号起连接作用
	var timer;
	function autoMove(){
		timer=setInterval(function(){
			//i++;
			if(i*388>=ulW){
				i=0;	
			}
			$('.GD').animate({marginLeft:-374*i+'px'});	
			$('.a a').eq(i).addClass('on').siblings().removeClass('on');
		},3000)	
	}
	autoMove();
	//鼠标放到slide上面停止滑动，移开继续滑动
	$('.a').hover(function(){
		clearInterval(timer);
	},function(){
		autoMove();
	})
	//点击数字显示相应的图片
	$('.a a').each(function(index) {
        //alert(index)
		$(this).click(function(){
			//alert(index)	
			//alert(n)
			i=index;
			$('.GD').animate({marginLeft:-374*i+'px'});	
			$('.a a').eq(i).addClass('on').siblings().removeClass('on');;
		})
		})
	
	  })