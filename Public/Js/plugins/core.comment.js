/**
 * 扩展核心评论对象
 * @author icubit <icubit@qq.com>
 * @version agdQ-1.0
 */
core.comment = {
	// 给工厂调用的接口
	_init:function(attrs) {
		if(attrs.length == 3) {
			core.comment.init(attrs[1], attrs[2]);
		} else {
			return false;
		}
	},
	// 初始化评论对象
	init: function(attrs, commentContainerObj) {
		if("undefined" != typeof(attrs.feed_id)){
			this.feed_id = attrs.feed_id;
		}
		if("undefined" != typeof(attrs.to_comment_id)){
			this.to_comment_id = attrs.to_comment_id;
		}
		if("undefined" != typeof(attrs.to_uid)){
			this.to_uid = attrs.to_uid;
		}
		/*if("undefined" != typeof(attrs)){
			//如果定义了attrs,则必须有feed_id,	to_comment_id,to_uid参数
			if("undefined" == typeof(attrs.feed_id)
			|| "undefined" == typeof(attrs.to_comment_id)
			|| "undefined" == typeof(attrs.to_uid))
			{
				bResult = false;
			}
		}*/
		if("undefined" != typeof(attrs.to_comment_uname)){
			this.to_comment_uname = attrs.to_comment_uname;
		}

		/*this.feed_id = attrs.feed_id;
		this.to_comment_id = attrs.to_comment_id,
		this.to_uid = attrs.to_uid;*/

		if("undefined" != typeof(commentContainerObj)) {
			this.commentContainerObj = commentContainerObj;
		}

		/*this.app_uid = attrs.app_uid,
		this.row_id  = attrs.row_id,
		this.to_comment_id = attrs.to_comment_id,
		this.to_uid = attrs.to_uid;
		this.app_row_id = attrs.app_row_id;//原文ID
		this.addToEnd = "undefined" == typeof(attrs.addToEnd) ? 0 : attrs.addToEnd;
		this.canrepost = "undefined" == typeof(attrs.canrepost) ? 1 : attrs.canrepost;
		this.cancomment = "undefined" == typeof(attrs.cancomment) ?  1 : attrs.cancomment;
		this.cancomment_old = "undefined" == typeof(attrs.cancomment_old) ?  1 : attrs.cancomment_old;
		if("undefined" != typeof(attrs.app_name)) {
			this.app_name = attrs.app_name;
		} else {
			this.app_name = "public";	//默认应用
		}
		if("undefined" != typeof(attrs.table)) {
			this.table = attrs.table;
		} else {
			this.table = 'feed';	//默认表
		}
		if("undefined" != typeof(attrs.to_comment_uname)) {
			this.to_comment_uname = attrs.to_comment_uname;
		}
		if("undefined" != typeof(commentListObj)) {
			this.commentListObj = commentListObj;
		}*/
	},

	// 显示回复块
	display: function() {
		var commentContainerObj = this.commentContainerObj;
		var comment_list = commentContainerObj.childModels['comment_list'][0];
		comment_list.innerHTML = '<img src="'+PUBLIC_URL+'/Images/loading.gif" style="text-align:center;display:block;margin:0 auto;"/>';
		$(commentContainerObj).slideToggle('fast');
		$.post('/agdQ/index.php/Widget/comment',{feed_id:this.feed_id},function(msg){
			if(msg.status == 0){
  				//$('#feed'+feed_id+' .loading').html('没有更多了');

			}else{
				//$('#feed'+feed_id+' .loading').append(msg.data);
				//$('#feed'+feed_id+' .loading').replaceWith(msg.data);
				comment_list.innerHTML = msg.data;
				M(commentContainerObj);
				//@评论框
				//atWho($(commentContainerObj).find('textarea'));
				$(commentContainerObj).find('textarea').focus();
			}
		},'json');
	},
	/*display: function() {
		var commentListObj = this.commentListObj;
		if("undefined" == typeof this.table) {
			this.table = 'feed';
		}
		if(commentListObj.style.display == 'none') {
			if(commentListObj.innerHTML !=''){
				commentListObj.style.display = 'block';
			}else{
				var rowid = this.row_id;
				var appname = this.app_name;
				var table = this.table;
				var cancomment = this.cancomment;
				commentListObj.style.display = 'block';
				commentListObj.innerHTML = '<img src="'+THEME_URL+'/image/load.gif" style="text-align:center;display:block;margin:0 auto;"/>';
				$.post(U('widget/Comment/render'),{app_uid:this.app_uid,row_id:this.row_id,app_row_id:this.app_row_id,isAjax:1,showlist:0,
						cancomment:this.cancomment,cancomment_old:this.cancomment_old,app_name:this.app_name,table:this.table,
						canrepost:this.canrepost },function(html){
							if(html.status =='0'){
								commentListObj.style.display = 'none';
								ui.error(html.data)
							}else{
								commentListObj.innerHTML = html.data;
								$('#commentlist_'+rowid).html('<img src="'+THEME_URL+'/image/load.gif" style="text-align:center;display:block;margin:0 auto;"/>');
								$.post(U('widget/Comment/getCommentList'),{app_name:appname,table:table,row_id:rowid,cancomment:cancomment},function (res){
									$('#commentlist_'+rowid).html(res);
									M($('#commentlist_'+rowid).get(0));
								});
								M(commentListObj);
								//@评论框
								atWho($(commentListObj).find('textarea'));
								$(commentListObj).find('textarea').focus();
							}
				},'json');
			}
		}else{
			commentListObj.style.display = 'none';
		}
	},*/
	// 初始化回复操作
	initReply: function() {
		var mini_editor = this.commentContainerObj.childModels['mini_editor'][0];
		var _textarea = $(mini_editor).find('textarea');
		var html = '回复'+'@'+this.to_comment_uname+' ：';
		//_textarea.focus();

		//_textarea.inputToEnd(html);
		//一次只能回复一个人
		_textarea.val(html);
		_textarea.focus();
	},
	/*initReply: function() {
		this.comment_textarea = this.commentListObj.childModels['comment_textarea'][0];
		var mini_editor = this.comment_textarea.childModels['mini_editor'][0];
		var _textarea = $(mini_editor).find('textarea');
		var html = L('PUBLIC_RESAVE')+'@'+this.to_comment_uname+' ：';
		//_textarea.focus();
		_textarea.inputToEnd(html);
		_textarea.focus();
	},*/
	// 发表评论
	addComment:function(afterComment,obj){

		var commentContainerObj = this.commentContainerObj;
		var mini_editor = commentContainerObj.childModels['mini_editor'][0];
		var _textarea = $(mini_editor).find('textarea').get(0);
		var strlen = core.getLength(_textarea.value);
		var leftnums = initNums - strlen;
		if(leftnums < 0 || leftnums == initNums) {
			flashTextarea(_textarea);
			return false;
		}
		var content = _textarea.value;
		if(content == '') {
			alert('不能为空');
			//ui.error(L('PUBLIC_CONCENT_TIPES'));
		}
		if("undefined" != typeof(this.addComment) && (this.addComment == true)) {
			return false;	//不要重复评论
		}
		var addcomment = this.addComment;
		//var addToEnd = this.addToEnd;

		var _this = this;
		//obj.innerHTML = '回复中..';
		$(obj).find('button').get(0).innerHTML='回复中..';

		var args = M.getEventArgs(obj);
		var feed_id = args.feed_id;
		var to_comment_id = args.to_comment_id;
		var content = $(obj.parentModel).find('textarea').get(0).value;
		$.post('/agdQ/index.php/Widget/addComment',{
			feed_id:this.feed_id,
			content:content,
			to_comment_id:this.to_comment_id,
			to_uid:this.to_uid,
			},function(msg){
				if(msg.status == "0"){
					alert(msg.info);
				}else{
					
					var comment_list = commentContainerObj.childModels['comment_list'][0];
					$(comment_list).prepend(msg.data);
					//M(commentContainerObj);
					if ( obj != undefined ){
						$(obj).find('button').get(0).innerHTML='回复';
					}
					//更新评论数
					updateCount(_this.feed_id,'comment');
					//重置
					_textarea.value = '';
					/*_this.to_comment_id = 0;
					_this.to_uid = 0;*/
					M.setEventArg(obj,'to_comment_id',0);
					M.setEventArg(obj,'to_uid',0);

					M(commentContainerObj);
					/*if("function" == typeof(afterComment)){
						afterComment();
					}*/
				}
			},'json');
	},
	/*addComment:function(afterComment,obj) {
		var commentListObj = this.commentListObj;
		this.comment_textarea = commentListObj.childModels['comment_textarea'][0];
		var mini_editor = this.comment_textarea.childModels['mini_editor'][0];
		var _textarea = $(mini_editor).find('textarea').get(0);
		var strlen = core.getLength(_textarea.value);
		var leftnums = initNums - strlen;
		if(leftnums < 0 || leftnums == initNums) {
			flashTextarea(_textarea);
			return false;
		}
		// 如果转发到自己的微博
		var ischecked = $(this.comment_textarea).find("input[name='shareFeed']").get(0).checked;
		if(ischecked == true) {
			var ifShareFeed = 1;
		} else {
			var ifShareFeed = 0;
		}
		var isold = $(this.comment_textarea).find("input[name='comment']");
		var comment_old = 0;
		if( isold.get(0) != undefined) {
			if ( isold.get(0).checked == true  ){
				var comment_old = 1;
			}
		}
		var content = _textarea.value;
		if(content == '') {
			ui.error(L('PUBLIC_CONCENT_TIPES'));
		}
		if("undefined" != typeof(this.addComment) && (this.addComment == true)) {
			return false;	//不要重复评论
		}
		var addcomment = this.addComment;
		var addToEnd = this.addToEnd;

		var _this = this;
		obj.innerHTML = '回复中..';
		$.post(U('widget/Comment/addcomment'),{
			app_name:this.app_name,
			table_name:this.table,
			app_uid:this.app_uid,
			row_id:this.row_id,
			to_comment_id:this.to_comment_id,
			to_uid:this.to_uid,
			app_row_id:this.app_row_id,
			content:content,
			ifShareFeed:ifShareFeed,
			comment_old:comment_old
			},function(msg){
				if(msg.status == "0"){
					ui.error(msg.data);
				}else{
					if("undefined" != typeof(commentListObj.childModels['comment_list']) ){
						if(addToEnd == 1){
							$(commentListObj).find(' .comment_lists').eq(0).prepend(msg.data);
						}else{
							$(msg.data).insertBefore($(commentListObj.childModels['comment_list'][0]));
						}
					}else{
						$(commentListObj).find('.comment_lists').eq(0).html(msg.data);
					}
					M(commentListObj);
					if ( obj != undefined ){
						obj.innerHTML = '回复';
					}
					//重置
					_textarea.value = '';
					_this.to_comment_id = 0;
					_this.to_uid = 0;
					if("function" == typeof(afterComment)){
						afterComment();
					}
				}
				addComment = false;
			},'json');
	},*/
	delComment:function(comment_id){
		$.post(U('widget/Comment/delcomment'),{comment_id:comment_id},function(msg){
			//什么也不做吧
		});
	}
};
