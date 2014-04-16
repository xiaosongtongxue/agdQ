// JavaScript Document
/*
 * sft插件：切换页面
 * author:icubit(icubit@qq.com)
 * data:2013-9-9
 */
(function($) {
		
 	var pluginName = 'sft',
	options = {},
	defaults = {
		speed: 'slow',
	},
	$w = $(window),
	$windows = [],
	$color = ['success','info','primary','warning','danger'],
	back = null;

	var _init = function(){
		//
		$('[data-toggle=sft]').click(function(){
			var moveTo = $(this).attr('data-moveTo');
			if('undefined' == typeof(moveTo))
				return ;
			$.moveTo(moveTo);	
		});
		$('.window').each(function(index, element) {
			var coor = $(element).attr('coor');
			var temp = coor.split("-");
			var x = parseInt(temp[0]);
			var y =  parseInt(temp[1]);
			var temp_w = x*100;
			var temp_h = y*100;
			
			$(element).css({left:temp_w+'%',top:temp_h+'%'});
			//var rand = parseInt(6*Math.random())
			var t = index%6;
			$(element).addClass($color[t]);
			$(element).find('button.btn').addClass('btn-'+$color[t]);
			//$(element).find('a.back').css({left:temp_w+'%',top:temp_h+'%'});
			
			$windows.push($(element));
		});	
		_setCurWindow('0-0');
		$.moveTo('0-0',true);
		
	};
	//设置当前窗口
	var _setCurWindow = function(coor){
		var c = '';
		if('object' == typeof(coor))
			c = coor.attr('coor');
		if('string' == typeof(coor))
			c = coor;
			
		$.each($windows, function(i){
			this.data('curWindow',false);
			if(this.attr('coor') == c)
				this.data('curWindow',true);
        });	
	};
	//得到当前窗口jquery对象
	var _getCurWindow = function(){
		var $temp;
		$.each($windows, function(i){
			var t  = this.data('curWindow');
			if(this.data('curWindow'))
				$temp = this;
       
		});
		return $temp;
	};
	//得到窗口jquery对象
	var _getWindow = function(coor){
		var $temp;
		$.each($windows, function(i){
			if(this.attr('coor') == coor){
				$temp = this;
		//		return false;
			}
		});
		return $temp;
		//return false;
	};
	//参数：argument0:0-0&4-0  argument1:true/false 表示是否初始化
	$.extend({moveTo:function(){
		
		//保存返回路径	
		if('undefined' == typeof(arguments[1]) || arguments[1] == false){
			var $curWin = _getCurWindow();
			back = $curWin.attr('coor') + '&';
			back += arguments[0];
		}
		var args = arguments[0].split('&');
		var i=0;
		
		var sftPage = function(){
			var coor  = args[i];
			if('undefined' == typeof(coor)){
				clearInterval(loop);
				return ;	
			}
				
			$target = _getWindow(coor);
			var top = $target.offset().top;
			var left = $target.offset().left;
			$('html:not(:animated),body:not(:animated)').animate({scrollTop:top,scrollLeft:left }, options.speed);	
			_setCurWindow($target);
			i++;
		};
		
		var loop = setInterval(sftPage,800);
		
	}});
	//返回
	$.extend({pre:function(){
		if(back){
			var temp = back.split('&');
			temp = temp.reverse();
			temp.shift();
			var coor = temp.join('&');
			$.moveTo(coor);
		}
	
	}});
	//返回原点
	$.extend({goOriginal:function(){
		if(_getCurWindow().attr('coor') == '0-0')
			return ;
		$.moveTo('0-0');
	}});
	
	var _onResize = function(){
		var $curWin = _getCurWindow();
		var top = $curWin.offset().top;
		var left = $curWin.offset().left;
		$w.scrollTop(top);		
		$w.scrollLeft(left);
		//$.moveTo($curWin);
	};
	var _onScroll = function(event){
	//	event.preventDefault();
	};
	/*$.extend({pluginName:function(customOptions){
		// 代码在此处运行
 		options = $.extend( {}, defaults, customOptions);
		_init();
		$w.resize(_onResize);
		$w.scroll(_onScroll);
	}});*/
 	$.fn[pluginName] = function(customOptions) {
 
        // 代码在此处运行
 		options = $.extend( {}, defaults, customOptions);
		_init();
		$w.resize(_onResize);
		$w.scroll(_onScroll);
		//_moveTo('0-0');
    };

})(jQuery);