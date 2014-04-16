/*
 * login插件：登录页面
 * author:xiaoluo(584188065@qq.com)
 * date:2013-10-29
 */
$(document).ready(function() { 
	// 提交表单
	$('#ajax_login_form').submit(function(){
		$(this).ajaxSubmit({
			beforeSubmit: checkLoginForm,
			success: loginCallback,
			dataType: 'json'
		});
		return false;
	});
    // 提交发布前验证
	var checkLoginForm = function(){
		if($('#account_input').val().length == 0){
			$.scojs_message('请输入账号!', $.scojs_message.TYPE_ERROR);
			$('#account_input').focus();
			return false;
		}
		if($('#pwd_input').val().length == 0){
			$.scojs_message('请输入密码!', $.scojs_message.TYPE_ERROR);
			$('#pwd_input').focus();
			return false;
		}
		return true;
	}
    // 成功后的回调函数
	var loginCallback = function(i){
		if(i.status == 1){
			window.location.href = "/agdQ/index.php/Index/index";
			//$.scojs_message(i.info, $.scojs_message.TYPE_OK);
		}else{
			$.scojs_message(i.info, $.scojs_message.TYPE_ERROR);
		}
	}
	
	//找回密码提交表单
	$('#ajax_forgetPwd_form').submit(function(){
		$(this).ajaxSubmit({
			beforeSubmit: checkForgetPwdForm,
			success: forgetPwdCallback,
			dataType: 'json'
		});
		return false;
	});
    // 提交发布前验证
	var checkForgetPwdForm = function(){
		if($('#forgetPwd_input').val().length == 0){
			$.scojs_message('请输入邮箱!', $.scojs_message.TYPE_ERROR);
			$('#forgetPwd_input').focus();
			return false;
		}
		return true;
	}
    // 成功后的回调函数
	var forgetPwdCallback = function(i){
		if(i.status == 1){
			$.scojs_message(i.info, $.scojs_message.TYPE_OK);
			setTimeout("window.location.href = '/agdQ/index.php'",3000);
			//$.scojs_message(i.info, $.scojs_message.TYPE_OK);
		}else if(i.status == 0){
			$.scojs_message(i.info, $.scojs_message.TYPE_ERROR);
		}
	}
}); 