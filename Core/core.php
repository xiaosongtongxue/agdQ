<?php
/* if (!defined('SITE_PATH')) exit();

date_default_timezone_set('UTC');

$time_include_start = microtime(TRUE);
$mem_include_start = memory_get_usage(); */

//设置全局变量ts
/* $yf['_debug']	=	true;		//调试模式
 $yf['_define']	=	array();	//全局常量
$yf['_config']	=	array();	//全局配置
$yf['_access']	=	array();	//访问配置
$yf['_router']	=	array();	//路由配置 */

// 当前文件名
/* if(!defined('_PHP_FILE_')) {
 if(IS_CGI) {
// CGI/FASTCGI模式下
$_temp  = explode('.php',$_SERVER["PHP_SELF"]);
define('_PHP_FILE_', rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
}else {
define('_PHP_FILE_', rtrim($_SERVER["SCRIPT_NAME"],'/'));
}
} */


defined('IS_CGI',substr(PHP_SAPI, 0, 3)=='cgi' ? 1 : 0 );
/*defined('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
defined('IS_HTTPS',0);*/

// 当前文件名
if(!defined('_PHP_FILE_')) {
	if(IS_CGI) {
		// CGI/FASTCGI模式下
		$_temp  = explode('.php',$_SERVER["PHP_SELF"]);
		define('_PHP_FILE_', rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
	}else {
		define('_PHP_FILE_', rtrim($_SERVER["SCRIPT_NAME"],'/'));
	}
}

// 网站URL根目录
if(!defined('__ROOT__')) {	
	$_root = dirname(_PHP_FILE_);
	define('__ROOT__',  (($_root=='/' || $_root=='\\')?'':rtrim($_root,'/')));
}

//基本常量定义

/* */
define('SITE_DOMAIN'			,	strip_tags($_SERVER['HTTP_HOST']));
//define('SITE_URL'				,	(IS_HTTPS?'https:':'http:').'//'.SITE_DOMAIN.__ROOT__);
define('SITE_URL'				,	'http:'.'//'.SITE_DOMAIN.__ROOT__);

define('APPS_PATH'				,	SITE_PATH.'Apps');//注意，SITE_PATH的最后一个字符是/
define('APPS_URL'				,	SITE_URL.'/Apps');

define('ADDON_PATH'				,	SITE_PATH.'Addons');//注意，SITE_PATH的最后一个字符是/
define('ADDON_URL'				,	SITE_URL.'/Addons');

define('PUBLIC_PATH'			,	SITE_PATH.'Public');//注意，SITE_PATH的最后一个字符是/
define('PUBLIC_URL'				,	SITE_URL.'/Public');

define('IMAGE_PATH'				,	SITE_PATH.'Public/Images');//注意，SITE_PATH的最后一个字符是/
define('IMAGE_URL'				,	SITE_URL.'/Public/Images');

define('UPLOAD_PATH'			,	SITE_PATH.'Public/Uploads');//注意，SITE_PATH的最后一个字符是/
define('UPLOAD_URL'				,	SITE_URL.'/Public/Uploads');

define('UPLOAD_IMAGE_PATH'		,	SITE_PATH.'Public/Uploads/Images');//注意，SITE_PATH的最后一个字符是/
define('UPLOAD_IMAGE_URL'		,	SITE_URL.'/Public/Uploads/Images');
//附件路径
define('ATTACH_PATH'			,	SITE_PATH.'Public/Uploads/Attach');//注意，SITE_PATH的最后一个字符是/
define('ATTACH_URL'				,	SITE_URL.'/Public/Uploads/Attach');

define('AVATAR_PATH'			,	SITE_PATH.'Public/Uploads/Images/Avatar');//注意，SITE_PATH的最后一个字符是/
define('AVATAR_URL'				,	SITE_URL.'/Public/Uploads/Images/Avatar');
?>	