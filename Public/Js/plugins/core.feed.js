// 微博核心Js操作
core.feed = {
	_init:function(){
		return true;
	},
	// 微博初始化
	init:function(agrs) {
		this.firstPage = 1;
		this.curPage = 1;
		this.lastPage = args.lastPage;
		
		this.newNums = args.newNums;
		this.initNums = agrs.initNums;		// 微博字数
		this.maxId = args.firstId;			// 最大微博ID
		this.loadId = args.loadId;			// 载入的微博ID
		//this.feedType = args.feedType,		// 微博类型
		//this.loadmore = args.loadmore,		// 是否载入更多
		this.loadmore = 1;	// 是否载入更多
		this.uid = args.uid;				// 当前微博列表对应的UID
		this.cid = args.cid;
		this.loadnew = args.loadnew;		// 是否载入最新
		this.feed_type = args.feed_type;
		//this.feed_key = args.feed_key;
		this.firstId = args.firstId;		//第一个feed的ID,后面新加的不算
		//this.lastId = 0;
		//this.topic_id = args.topic_id;		// 是否为话题
		//this.pre_page = "undefined" == typeof(pre_page) ? 1 :pre_page;//分页用到的前一页
		if("undefined" == typeof(this.loadCount)) {
			this.loadCount = 0;
		}
		if(this.loadmore == 1) {
			this.canLoading = true;		// 当前是否允许加载
			core.feed.bindScroll();

		} else {
			this.canLoading = false;	// 当前是否允许加载
		}
		
		

		if($('#feed_list').length > 0 && this.canLoading){
			$('#feed_list').append("<div class='loading' id='loadMore'>" + "载入中..." + "<img src='" + PUBLIC_URL + "/Images/loading.gif' class='load'></div>");
			core.feed.loadMoreFeed();
			// 启动查找最新的loop
			this.startNewLoop();
		}
	},

	saveModelObj: function(obj){
		this.modal = obj;
	},
	// 页底加载微博
	bindScroll: function() {
		var _this = this;
		$(window).bind('scroll', function() {
			// 加载4次后，将不能自动加载微博
			if(_this.loadCount >= 4 || _this.canLoading == false){
				return false;
			}
			var bodyTop = document.documentElement.scrollTop + document.body.scrollTop;
			var bodyHeight = $(document.body).height();
			if(bodyTop + $(window).height() >= bodyHeight - 250) {
				//_this.loadCount = _this.loadCount + 1;
				if($('#feed_list').length > 0){
					$('#feed_list').append("<div class='loading' id='loadMore'>" + "载入中..." + "<img src='" + PUBLIC_URL + "/Images/loading.gif' class='load'></div>");
					core.feed.loadMoreFeed();
				}
			}
		});
	},
	// 加载更多feed,每页feed最多只能加载4次,每次10条
	loadMoreFeed: function(){
		var _this = this;
		/*if(_this.loadmore == 0)
			return false;*/
		_this.canLoading = false;
		// 获取微博数据
		var part = _this.loadCount+1;
		$.post('/agdQ/index.php/Widget/loadMoreFeed',{uid:_this.uid,cid:_this.cid,feed_type:_this.feed_type,curPage:_this.curPage,part:part,firstId:_this.firstId},function(msg){
			//加载失败
			if(msg.status == 0 ){
				$('#loadMore').remove();
				//if(msg.status == 0 && ("undefined" != typeof(msg.msg)) && _this.loadmore > 0) {
					$('#feed_list').append('<div class="loading" id="loadMore">' + msg.info + '</div>');
				//}
			//加载成功
			}else{
				_this.loadCount = _this.loadCount + 1;
				$('#loadMore').remove();
				$('#feed_list').append(msg.data);
				_this.canLoading = true;
				//_this.lastId = msg.minId;
				/*if(_this.firstId==0)
					_this.firstId = msg.maxId;*/
				/*var loadstr = '加载更多';*/
				if(_this.loadCount>=4){
					//_this.loadmore = 0;
					_this.canLoading = false;
					var loadstr = '没有更多了';
					$('#feed_list').append('<div class="loading" id="loadMore">' + loadstr + '</div>');
					$.post('/agdQ/index.php/Widget/getPageBar',{first:_this.firstPage,cur:_this.curPage,last:_this.lastPage},function(msg){
						if(msg.status==1){
							$('#page').html(msg.data);
						}
						
					},'json');
				}	
			}
			M(document.getElementById('feed_list'));
		},'json');
		
	},
	
	loadMoreByPage: function(page) {
		var _this = this;
		_this.canLoading = false;
		
		if(page == 'first'){
			page = 1;
		}else if(page == 'prev'){
			page = _this.curPage - 1;
		}else if(page == 'next'){
			page = _this.curPage + 1;
		}else if(page == 'last'){
			page = _this.lastPage;
		}else{
			
		}
		//$('#feed_list').html("<div class='loading' id='loadMore'>"+L('PUBLIC_LOADING')+"<img src='"+THEME_URL+"/image/load.gif' class='load'></div>");
		$('#feed_list').html("<div class='loading' id='loadMore'>" + "载入中..." + "<img src='" + PUBLIC_URL + "/Images/loading.gif' class='load'></div>");
		$('#page').html('');
		//scrolltotop.scrollup();
		$.post('/agdQ/index.php/Widget/loadMoreByPage',{uid:_this.uid,cid:_this.cid,feed_type:_this.feed_type,firstId:_this.firstId,page:page,last:_this.lastPage},function(msg){
			if(msg.status == 1){
				$('#feed_list').html(msg.data);
				$('#page').html(msg.page_html);
				
			}
			_this.curPage = page;
			
		},'json');
		M(document.getElementById('feed_list'));
	},
	/*loadMoreFeed: function() {
		var _this = this;
		_this.canLoading = false;
		// 获取微博数据
		$.get(U('widget/FeedList/loadMore'), {'loadId':_this.loadId, 'type':_this.feedType, 'uid':_this.uid, 'feed_type':_this.feed_type, 'feed_key':_this.feed_key, 'fgid':fgid, 'topic_id':_this.topic_id, 'load_count':_this.loadCount}, function(msg) {
			// 加载失败
			if(msg.status == "0" || msg.status == "-1") {
				$('#loadMore').remove();
				if(msg.status == 0 && ("undefined" != typeof(msg.msg)) && _this.loadmore > 0) {
					$('#feed-lists').append('<div class="loading" id="loadMore">' + msg.msg + '</div>');
				}
			}
			// 加载成功
			if(msg.status == "1") {
				if(msg.firstId > 0 && _this.loadnew == 1) {
					_this.firstId = msg.firstId;
					// 启动查找最新的loop
					_this.startNewLoop();
				}
				$('#loadMore').remove();
				if(_this.loadCount >= 4) {
					var $lastDl = $('<div></div>');
					$lastDl.html(msg.html);
					msg.html = $lastDl.find('dl').filter('.feed_list').slice(30);
				}
				$('#feed-lists').append(msg.html);
				_this.canLoading = true;
				_this.loadId = msg.loadId;
				if(_this.loadCount >= 4) {
					$('#feed-lists').append('<div id="page" class="page" style="display:none;">' + msg.pageHtml + '</div>');
					if($('#feed-lists .page').find('a').size() > 2) {
						// 4ping + next 说明还有30个以上
						var href = false;
						$('#feed-lists .page').find('a').each(function() {
							href = $(this).attr('href');
						});
						// 重组分页结构
						$('#feed-lists .page').html(msg.pageHtml).show();
						$('#feed-lists .page').find('a').each(function() {
							var href = $(this).attr('href');
							if(href) {
								$(this).attr('href', 'javascript:;');
								$(this).click(function() {
									core.weibo.loadMoreByPage(href);
								});
							}
						});
					} else {
						if($('#feed-lists').find('dl').size() > 0) {
							$('#feed-lists').append('<div class="loading" id="loadMore">' + L('PUBLIC_ISNULL') + '</div>');
						}
					}
				} else {
					core.weibo.bindScroll();
				}
				M(document.getElementById('feed-lists'));
			}
		}, 'json')
		return false;
	},*/
	// 分页加载更多数据
	/*loadMoreByPage: function(href) {
		var obj = this;
		obj.canLoading = false;
		$('#feed-lists').html("<div class='loading' id='loadMore'>"+L('PUBLIC_LOADING')+"<img src='"+THEME_URL+"/image/load.gif' class='load'></div>");
		
		scrolltotop.scrollup();
		$.get(href,{},function(msg){
			if(msg.status == "0" || msg.status == "-1"){
				$('#feed-lists').append("<div class='load' id='loadMore'>'+L('PUBLIC_ISNULL')+'</div>");
			}else{
				$('#feed-lists').html(msg.html);
				$('#feed-lists').append('<div id="page" class="page" >'+msg.pageHtml+'</div>');

				$('#feed-lists .page').find('a').each(function(){
					var href = $(this).attr('href');
					if(href){
						$(this).attr('href','javascript:void(0);');
						$(this).click(function(){
							core.weibo.loadMoreByPage(href);
						});
					}
				});
				//core.weibo.bindScroll();
				M(document.getElementById('feed_list'));
			}
		},'json');
		return false;
	},*/
	
	// 加载最新微博
	startNewLoop: function() {
		var _this = this;
		var searchNew = function() {
			if(_this.firstId < 1) {
				return false;
			}
			
			// 加载最新的数据
			$.post('/agdQ/index.php/Widget/getNewCount', {uid:_this.uid,cid:_this.cid,maxId:_this.maxId, feed_type:_this.feed_type}, function(msg) {
				if(msg.status == 1) {
					if(msg.count == 0)
						return false;
					_this.showNew(msg.count);
					//_this.tempHtml = msg.data;
					//_this.tmpfirstId = msg.maxId;
					_this.newNums = msg.count;
				}
				/*else if(msg.status == 2){
					_this.showNew(msg.count);
					_this.newNums = msg.count;
				}*/
			}, 'json');
		};
		// 每2分钟查找一次最新微博
		var loop = setInterval(searchNew, 120000);
	},
	// 提示有多少新微博数据
	showNew: function(nums) {
		
		if($('#feed_container').find('#newInfo').length > 0) {
			
			$('#newInfo').find('a').html(nums+'条新信息');
		} else {
			var html = '<div class="well" id="newInfo"><a href="javascript:core.feed.showNewList()">'+nums+'条新信息</a></div>';
			//var html = '<a href="javascript:core.weibo.showNewList()" class="notes">'+L('PUBLIC_WEIBO_NUM',{'sum':nums})+'</a>';
			$('#feed_container').prepend(html);
			//M(document.getElementById('newInfo'));
		}
	},
	showNewList:function(){
		var _this = this;
		$.post('/agdQ/index.php/Widget/loadNew', {uid:_this.uid,cid:_this.cid,maxId:_this.maxId, feed_type:_this.feed_type}, function(msg) {
			if(msg.status == 2){
				document.location.reload();	
				return ;
			}else{
				$('#newInfo').remove();
				$('#feed_list').prepend(msg.data);
				_this.maxId = msg.maxId;
				M(document.getElementById('feed_list'));
			}
		},'json');
		
	},
	// 发布微博之后操作
	afterPost: function(obj, textarea, close) {
		textarea.value = '';
		obj.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = L('PUBLIC_INPUT_TIPES',{'sum':'<span>'+initNums+'</span>'});
		var fadeOutObj = function() {
			textarea.ready = null;
		};

		$(obj.childModels['post_ok'][0]).fadeOut(500,fadeOutObj);
		// 修改微博数目
		if("undefined" == typeof(close) || !close) {
			updateUserData('weibo_count',1);
		}
		if("undefined" != typeof(core.uploadFile)) {
			core.uploadFile.removeParentDiv();
		}
		if("undefined" != typeof core.contribute) {
			core.contribute.resetBtn();
		}
	},
	// 将json数据插入到feed-lists中
	insertToList: function(html, feedId) {
		if("undefined" == typeof(html) || html == '') {
			return false;
		}
		if($('#feed-lists').length > 0) {
			var before = $('#feed-lists dl').eq(0);
			$dl = $('<dl></dl>');
			$dl.attr('model-node', 'feed_list');
			$dl.attr('id', 'feed'+feedId);
			$dl.addClass('feed_list');
			$dl.html(html);
			if(before.length > 0) {
				$dl.insertBefore(before);
			} else {
				if($('#feed-lists').find('dl').size() > 0) {
					$('#feed-lists').append($dl);
				} else {
					$('#feed-lists').html($dl);
				}
			}
			M($dl[0]);
		}
		//DIY专用
		if($('#feed-lists-d').length > 0) {
			var before = $('#feed-lists-d dl').eq(0);
			var _dl = document.createElement('dl');
			_dl.setAttribute('class', 'feed_list');
			_dl.setAttribute('model-node', 'feed_list');
			_dl.setAttribute('id', 'feed'+feedId);
			_dl.innerHTML = html;
			if(before.length > 0) {
				$(_dl).insertBefore(before);
			} else {
				if($('#feed-lists-d').find('dl').size() > 0) {
					$('#feed-lists-d').append(_dl);
				} else {
					$('#feed-lists-d').html(_dl);
				}
			}
			M(_dl);
		}
	},
	// 检验微博内容，obj = 要验证的表单对象，post = 表示是否发布
	checkNums: function(obj, post) {
		if("undefined" == typeof(obj.parentModel.parentModel.childModels['numsLeft'])) {
			return true;
		}
		// 获取输入框中还能输入的数字个数
		var strlen = core.getLength(obj.value , true);
		var leftNums = initNums - strlen;
		if(leftNums == initNums && 'undefined' != typeof(post)) {
			return false;
		}
		// 获取按钮对象
		var objInput = '';
		if($(obj.parentModel.parentModel.childModels['send_action']).html() != null) {
			objInput = $(obj.parentModel.parentModel.childModels['send_action'][0]).find('a').eq(0);
		}
		// 获取剩余字数
		if(leftNums >= 0) {
			var html = (leftNums == initNums) ? L('PUBLIC_INPUT_TIPES', {'sum':'<span>'+leftNums+'</span>'}) : L('PUBLIC_PLEASE_INPUT_TIPES', {'sum':'<span>'+leftNums+'</span>'});
			obj.parentModel.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = html;
			$(obj).removeClass('fb');
			if(leftNums == initNums && $(obj).find('img').size() == 0) {
				if(typeof(objInput) == 'object') {
					objInput[0].className = 'btn-grey-white';
				}
				return false;	// 没有输入内容
			}
			if(typeof(objInput) == 'object') {
				objInput[0].className = 'btn-green-big';
			}
			return true;
		} else {
			var html = L('PUBLIC_INPUT_ERROR_TIPES', {'sum':'<span style="color:red">' + Math.abs(leftNums) + '</span>'});
			$(obj).addClass('fb');
			obj.parentModel.parentModel.parentModel.childModels['numsLeft'][0].innerHTML = html;
			if(typeof(objInput) == 'object') {
				objInput[0].className = 'btn-grey-white';
			}
			return false;
		}
	},
	// 发布/转发微博
	post_feed: function(_this, mini_editor, textarea) {
		var obj = this;
		// 避免重复发送
		if("undefined" == typeof(obj.isposting)) {
			obj.isposting = true;
		} else {
			if(obj.isposting == true) {
				return false;
			}
		}

		//var content = $(textarea).val();
		var args = M.getEventArgs(_this);
		// 为空处理
		var data = textarea.value;
		if(data == '' || data.length < 0) {
			// TODO 只有一次情况才会执行到这里面 一般是不会的
			//ui.error( L('PUBLIC_CENTE_ISNULL') );
			alert('不能为空');
			obj.isposting = false;
			return false;
		}
		//检查字数
		//if(obj.checkNums(textarea,'post') == false) {
			/*if(type == 'postimage') {
				textarea.value = L('PUBLIC_SHARE_IMAGES');
			} else if(type == 'postfile') {
				textarea.value = L('PUBLIC_SHARE_FILES');
			} else {
				flashTextarea(textarea);
				obj.isposting = false;
				return false;
			}*/
			//flashTextarea(textarea);
			/*alert('字数超过限制');
			obj.isposting = false;
			return false;
		}*/
		//检查完毕，一切OK
		var post_btn = mini_editor.parentModel.childModels['action'][0].childEvents['post_feed'][0];
		var btn_title = $(post_btn).html();
		$(post_btn).html(btn_title+"...");
		$.post('/agdQ/index.php/Feed/postFeed',{type:args.type,feed_id:args.feed_id,feed_type:args.feed_type,content:data},function(msg){
			if(msg.status==0){
				alert(msg.info);
			}else{
				if(args.type == 'repost'){
					
					var feed_list = mini_editor.parentModel.parentModel.childModels['feed_list'][0];
					$(feed_list).prepend(msg.data);
					obj.modal.close();
					//更新原Feed转发数
					updateCount(args.feed_id,'share');
					
					var share_id="#feed"+msg.maxId;
					//window.location.href=share_id;
				}else{
					var feed_list = mini_editor.parentModel.parentModel.childModels['feed_list'][0];
					$(feed_list).prepend(msg.data);
					//文本域置空
					$(mini_editor).find('textarea').get(0).value='';
				}
				
				
				obj.maxId = msg.maxId;
				$('#newInfo').remove();
				M(document.getElementById('feed_list'));
			}
			$(post_btn).html(btn_title);
			obj.isposting = false;
		},'json');
	},
	/*	post_feed: function(_this, mini_editor, textarea, isbox ) {

		var obj = this;
		// 避免重复发送
		if("undefined" == typeof(obj.isposting)) {
			obj.isposting = true;
		} else {
			if(obj.isposting == true) {
				return false;
			}
		}

		if("undefined" == typeof(isbox)) {
			isbox = false;
		}
		// 微博类型在此区分
		var attrs = M.getEventArgs(_this);
		var attachobj = $(_this.parentModel).find('.attach_ids');
		if(attachobj.length > 0) {
			var type = (attachobj.attr('feedtype') == 'image') ? 'postimage' : 'postfile';
			var attach_id = attachobj.val();
		} else {
			var attach_id = '';
			var type = attrs.type;
		}
		var app_name = attrs.app_name;
		if(obj.checkNums(textarea,'post') == false) {
			if(type == 'postimage') {
				textarea.value = L('PUBLIC_SHARE_IMAGES');
			} else if(type == 'postfile') {
				textarea.value = L('PUBLIC_SHARE_FILES');
			} else {
				flashTextarea(textarea);
				obj.isposting = false;
				return false;
			}
		}
		// 获取投稿ID
		var channel_id = $.trim($('#contribute').val());
		// 为空处理
		var data = textarea.value;
		if(data == '' || data.length < 0) {
			// TODO 只有一次情况才会执行到这里面 一般是不会的
			ui.error( L('PUBLIC_CENTE_ISNULL') );
			obj.isposting = false;
			return false;
		}
		data = removeHTMLTag(data);
		if(data == '' || data.length < 0) {
			ui.error('请勿输入非法与敏感字符');
			obj.isposting = false;
			return false;
		}
		// 发布微博
		$.post(U('public/Feed/PostFeed'), {body:data, type:type, app_name:app_name, content:'', attach_id:attach_id, channel_id:channel_id}, function(msg) {
			obj.isposting = false;
			_this.className = 'btn-grey-white right';
			$(_this).html('<span>' + L('PUBLIC_SHARE_BUTTON') + '</span>');
			if(msg.status == 1) {
				if("undefined" != typeof(core.uploadFile)) {
					core.uploadFile.clean();
				}
				var postOk = mini_editor.childModels['post_ok'][0];
				$(postOk).fadeIn('fast');
				core.weibo.afterPost(mini_editor,textarea);
				if(!isbox) {
					core.weibo.insertToList(msg.data, msg.feedId);
				} else {
					ui.box.close();
					var mini = M.getModelArgs(mini_editor);
					ui.success(mini.prompt);
					if(document.getElementById('feed-lists') != null) {
						setTimeout(function() {
							core.weibo.insertToList(msg.data, msg.feedId);
						}, 1500);
					}
				}
			} else {
				ui.error(msg.data);
			}
		}, 'json');
		return false;
	}*/
};