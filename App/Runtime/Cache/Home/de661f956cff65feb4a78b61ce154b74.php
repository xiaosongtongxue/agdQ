<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($title); ?></title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
<link href="__PUBLIC__/Css/padding.css" rel="stylesheet">
<link href="__PUBLIC__/Css/scojs.css" rel="stylesheet">
</head>
<style>
html,body, .container { height: 100%; }
body > .container { height: auto; min-height: 100%; }
#footer {clear: both;position: relative;z-index: 10; height:150px; margin-top:-150px;}
#container { padding-bottom:150px; }
/* 样式部分 */
#footer { color: white; text-align: center;}
.yf-panel {
	/*position: relative;*/
	padding: 10px;
	margin: 5px 5px 5px 5px;
	background-color: #ffffff;
	border-radius: 4px;
	/*-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);*/
}
.font-style{
	font-size:24px;
}
</style>
<body background="__PUBLIC__/Uploads/Images/Background/3.jpg">
	<div class="container">
		<br />
		<br />
		<br />
		<div class="row">
			<div class="col-md-6">
				<div class="col-md-push-6">
					<img src="__PUBLIC__/Images/logo2.png" />
				</div>
			</div>
			<div class="col-md-5">
				<div class="yf-panel">
	<div class="article-header font-style">
		账号登录
	</div>
	<div  class="article-content">
		<form id="ajax_login_form" class="form-horizontal" role="form" action="__URL__/doLogin" method="post" enctype="application/x-www-form-urlencoded">
			<div class="form-group">
				<div class="col-md-offset-2 col-md-8">
					<input id="account_input" name="identity" type="text" class="form-control" placeholder="邮箱/学号">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-2 col-md-8">
					<input id="pwd_input" name="credential" type="password" class="form-control" id="inputEmail3" placeholder="密码">
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<div class="checkbox">
						<input name="rememberme" type="checkbox" value="1">记住下次登录
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary">登录</button>
				</div>
			</div>
		</form>
	</div>
	<div class="article-footer">
		<div class="row">
			<div class="col-md-3 col-md-push-7">
				<a id="forget-pwd" href="__URL__/forgetPwd">忘记密码？</a>
			</div>
			<div class="col-md-2 col-md-push-6">
				<a id="register-account" href="javascript:void(0);">注册</a>
			</div>
			<div class="col-md-2 col-md-push-5">
				<a id="yijian-feedback" href="javascript:void(0);">意见反馈</a>
			</div>
		</div>
	</div>
</div>
			</div>
		</div>
	</div>
	<div id="footer">	
		<div>
			<p>
				<a href="javascript:void(0);" target="_blank">官方网站</a> |
				<a href="javascript:void(0);" target="_blank">官方微博</a> |
				<a href="javascript:void(0);" target="_blank">官方微信</a> |
				<a href="javascript:void(0);" target="_blank">关于我们</a> |
				<a href="javascript:void(0);" target="_blank">加入我们</a> |
				<a href="javascript:void(0);" target="_blank">反馈建议</a>
			</p>
			<p>Copyright &copy; 2012 - 2013 YunFou.<a href="javascript:void(0);" target="_blank" href="">All Rights Reserved.</a></p>
			<p>云否公司 <a href="javascript:void(0);" target="_blank">版权所有</a> <a href="javascript:void(0);" target="_blank">皖ICP备13001806号-1</a></p>
		</div>
	</div>
<script src="__PUBLIC__/Js/jquery.min.js"></script>
<script src="__PUBLIC__/Js/jquery.form.js"></script>
<script src="__PUBLIC__/Js/sco/sco.message.js"></script>
<script src="__PUBLIC__/Js/Login/login.js"></script>
</body>
</html>