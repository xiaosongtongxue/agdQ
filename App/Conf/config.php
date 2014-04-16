<?php
if (!defined('SITE_PATH')) exit();

return array(
	// 数据库常用配置
	'DB_TYPE'			=>	'mysql',			// 数据库类型

	'DB_HOST'			=>	'localhost',		// 数据库服务器地址
	'DB_NAME'			=>	'agdq',			// 数据库名
	'DB_USER'			=>	'root',				// 数据库用户名
	'DB_PWD'			=>	'yf2013115',		// 数据库密码

	'DB_PORT'			=>	3306,				// 数据库端口
	'DB_PREFIX'			=>	'q_',				// 数据库表前缀（因为漫游的原因，数据库表前缀必须写在本文件）
	'DB_CHARSET'		=>	'utf8',				// 数据库编码


	'TMPL_L_DELIM'=>'<{',
	'TMPL_R_DELIM'=>'}>',

	'DEFAULT_THEME' => 'v3', //当前主题名称
	//'LAYOUT_ON' => true,

	'URL_MODEL'          => '1', //URL模式
	'APP_GROUP_LIST' => 'Home,Admin', //项目分组设定
	'DEFAULT_GROUP'  => 'Home', //默认分组
	//'TMPL_FILE_DEPR' => '&',
	// 显示页面Trace信息
	'SHOW_PAGE_TRACE' =>true,
	'LANG_SWITCH_ON' => true,   // 开启语言包功能


	//用于加密和解密函数使用
	'SECURE_CODE'		=>	'1125519de9dc2af35',	// 数据加密密钥
	//Cookie前缀 避免冲突
	'COOKIE_PREFIX'		=>	'agdQ_',	// 数据加密密钥

	// 显示页面Trace信息
	'SHOW_PAGE_TRACE' =>true,

	//管理员
	'SUPER_ADMIN'		=>	array('icubit'	=>'819089692@qq.com',
								  'icubit2'	=>'icubit@qq.com',
								  'xiaoluo'	=>'584188065@qq.com'),

	//RBAC配置
	'USER_AUTH_ON'              =>  true,
    'USER_AUTH_TYPE'			=>  2,		// 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY'             =>  'authId',	// 用户认证SESSION标记
    'ADMIN_AUTH_KEY'			=>  'administrator',
    'USER_AUTH_MODEL'           =>  'User',	// 默认验证数据表模型
    'AUTH_PWD_ENCODER'          =>  'md5',	// 用户认证密码加密方式
    'USER_AUTH_GATEWAY'         =>  '/Login/login',// 默认认证网关
    'NOT_AUTH_MODULE'           =>  '',	// 默认无需认证模块
    'REQUIRE_AUTH_MODULE'       =>  '',		// 默认需要认证模块
    'NOT_AUTH_ACTION'           =>  '',		// 默认无需认证操作
    'REQUIRE_AUTH_ACTION'       =>  '',		// 默认需要认证操作
    'GUEST_AUTH_ON'             =>  false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'             =>  0,        // 游客的用户ID
//    'DB_LIKE_FIELDS'            =>  'title|remark',
    'RBAC_ROLE_TABLE'           =>  'q_role',
    'RBAC_USER_TABLE'           =>  'q_role_user',
    'RBAC_ACCESS_TABLE'         =>  'q_access',
    'RBAC_NODE_TABLE'           =>  'q_node',
);