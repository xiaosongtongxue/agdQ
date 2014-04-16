<?php
/**
 * CommonAction 通用模块
 * @author  xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class CommonAction extends Action{

	protected	$uid = 0;

	/**
	 * 模块初始化，检查用户登陆
	 * @return void
	 */
	public function _initialize(){
		$this->checkLogin();
		
		$this->uid = $_SESSION[C('USER_AUTH_KEY')];
		//$this->assign('uid',$this->uid);
		//JS全局变量的赋值,暂时先放在这，等结构清楚后再调整
		$JS['SITE_URL'] 		= SITE_URL;
		$JS['UPLOAD_URL'] 		= UPLOAD_URL;
		$JS['APPNAME'] 			= APPNAME;
		$JS['UID'] 				= $_SESSION[C('USER_AUTH_KEY')];
		$JS['PUBLIC_URL']		= PUBLIC_URL;
		$JS['initNums']			= 140;
		$this->assign('js',$JS);
	}

	
	public function checkLogin(){
		if(!isset($_SESSION[C('USER_AUTH_KEY')]))
			redirect(U('Login/login'));
	}
	public function setTitle($title){
		$this->assign('title',$title);
		return $this;
	}
}
?>