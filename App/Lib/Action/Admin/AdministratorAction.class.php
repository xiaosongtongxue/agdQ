<?php
/**
 * 后台框架基类
 * @author icubit(icubit@qq.com)
 */
class AdministratorAction extends Action{
	protected  $navMenu = array(
		'System'	=>	array('name'=>'System','title'=>'系统','href'=>'__GROUP__/System'),
		'RBAC'		=>	array('name'=>'RBAC','title'=>'RBAC','href'=>'__GROUP__/RBAC'),
		'Content'	=>	array('name'=>'Content','title'=>'内容','href'=>'__GROUP__/Content'),
	);
	protected $_curMenu = null; 
	//abstract public function render($data);
	
	public function _initialize(){
		//检查登陆
		R('Home/Common/checkLogin');
		//if(isset($_SESSION['_NODE_LIST'][stroupper(APP_NAME)][stroupper(MODULE_NAME)][stroupper(ACTION_NAME)])){
			$this->checkRbac();		// 初始化的时候检查用户权限
		//}
		$this->assign('navMenu',$this->navMenu);
		
	}
	
	public function setCurMenu($menu){
		//$this->_curMenu = $menu;
		$this->assign('curMenu',$menu);
		return $this;
	}
	public function setTitle($title){
		$this->assign('title',$title);
		return $this;
	}
	
	/**
	 * 检查用户权限
	 */
	protected function checkRbac(){
		import('ORG.Util.Cookie');
		// 用户权限检查
		if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
			import('ORG.Util.RBAC');
			if (!RBAC::AccessDecision()) {
				//检查认证识别号
				if (!$_SESSION [C('USER_AUTH_KEY')]) {
					//跳转到认证网关
					redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
				}
				// 没有权限 抛出错误
				if (C('RBAC_ERROR_PAGE')) {
					// 定义权限错误页面
					redirect(C('RBAC_ERROR_PAGE'));
				} else {
					if (C('GUEST_AUTH_ON')) {
						$this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
					}
					// 提示错误信息
					$this->error(L('_VALID_ACCESS_'));
				}
			}
		}
	}
}