<?php
//初始化，进行加载
//网站根路径设置
define('SITE_PATH', str_replace("\\","/",dirname(__FILE__).'/'));

//载入核心文件
require(SITE_PATH.'Core/core.php');

define('THINK_PATH','./ThinkPHP/');

//公共项目（前台）
define('APP_PATH','./APP/');

//公共项目名称
define('APP_NAME','App');

define('APP_DEBUG',true);//true表示启用调试模式，false表示启用不调试模式(默认状态)

require THINK_PATH.'ThinkPHP.php';

?>
