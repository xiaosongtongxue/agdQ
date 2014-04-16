/**
 * 收藏模型Js核心插件
 * @author icubit <icubit@qq.com>
 * @version agdQ1.0
 */
core.collection = {
	// 初始化参数
	_init: function(attrs) {
		// 转化为数组
		attrs = $.makeArray(attrs);
		if(typeof attrs[4] == 'undefined') {
			//type:c_page时表示在收藏页面,f_page表示在信息流页面
			attrs.push('f_page');
		}
		if(attrs.length == 5) {
			core.collection.init(attrs[1],attrs[2],attrs[3],attrs[4]);
		} else {
			return false;
		}
	},
	init: function(obj,sid, stable ,type/*, sapp, isIco*/) {
		// 参数验证
		if('undefined'==typeof(obj) || 'undefined'==typeof(sid) || 'undefined'==typeof(stable)/* || 'undefined'==typeof(sapp)*/ ) {
			//ui.error(L('PUBLIC_TIPES_ERROR'));
			alert('参数错误,收藏失败');
			return false;
		}
		// 添加收藏操作
		if($(obj).attr('rel') == 'add') {
			$.post('/agdQ/index.php/Collection/addCollection', {source_id:sid, source_table_name:stable}, function(msg) {
				if(msg.status == 0) {
					//ui.error(msg.data);
					alert(msg.info);
				} else {
					// 设置对象操作属性
					$(obj).attr('rel', 'remove');

					/*if($('.count_' + stable + '_' + sid).length > 0) {
						if(isIco == 1) {
							$(obj).find('i').eq(0).addClass('current');
						} else {
							$(obj).html(L('PUBLIC_FAVORITED'));
						}
						var nums = $('.count_' + stable + '_' + sid).html();
						$('.count_' + stable + '_' + sid).html(parseInt(nums) + 1);
					} else {
						$(obj).html(L('PUBLIC_DEL_FAVORITE'));
					}*/
					//更新更新用户信息收藏数量,待做
					//updateUserData('favorite_count', 1);
					//ui.success(L('PUBLIC_FAVORITE_SUCCESS'));
					//更新当前feed的收藏数量,加一动画待做
					var span1 = $(obj).find('span').get(0);
					span1.innerHTML = '取消收藏';
					
					var span2 = $(obj).find('span').get(1);
					var cur_num = span2.innerHTML;
					if(cur_num=='')
						cur_num='0';
					span2.innerHTML = parseInt(cur_num) + 1;

					alert('收藏成功');
				}
			}, 'json');
			return false;
		}
		// 删除收藏操作
		if($(obj).attr('rel') == 'remove') {
			$.post('/agdQ/index.php/Collection/delCollection',{source_id:sid,source_table_name:stable},function(msg){
				if(msg.status == 1){
					//更新用户信息收藏数量,待做
					//updateUserData('favorite_count',-1);
					if(type !='c_page'){
						$(obj).attr('rel','add');
						/*if(isIco == 1) {
							$(obj).find('i').eq(0).removeClass('current');
						} else {
							$(obj).html(L('PUBLIC_FAVORITE'));
						}
						if($('.count_'+stable+'_'+sid).length >0 ){
							var nums = 	$('.count_'+stable+'_'+sid).html();
							$('.count_'+stable+'_'+sid).html(parseInt(nums)-1);
						}*/
						var span1 = $(obj).find('span').get(0);
						span1.innerHTML = '收藏';
						//更新当前feed的收藏数量,减一动画待做
						var span2 = $(obj).find('span').get(1);
						var cur_num = span2.innerHTML;
						var result_num = parseInt(cur_num) - 1;
						if(result_num == 0)
							span2.innerHTML = '';
						else
						 	span2.innerHTML = result_num;
						
						
					}else{//待做
						$('#feed'+sid).fadeOut('slow');
					}
				}else{
					//ui.error(msg.data);
					alert(msg.info);
				}
			},'json');
			return false;
		}
	}
};