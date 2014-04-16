<?php
// 后台用户管理模块
class UserAction extends AdministratorAction {
	protected $_tab = array(
		'User'		=>array('name'=>'User','title'=>'用户管理','href'=>'__URL__/user'),
		'Group'		=>array('name'=>'Group','title'=>'用户组管理','href'=>'__URL__/group'),
		'Verify'	=>array('name'=>'Verify','title'=>'用户认证','href'=>'__URL__/verify'),
		'AddUser'	=>array('name'=>'AddUser','title'=>'添加用户','href'=>'__URL__/addUser'),
			
	);
	public function _initialize(){
		parent::_initialize();
		$this->setCurMenu('User');
		$this->assign('tabs',$this->_tab);
		$this->setCurTab('User');
	}
	
	 private function setCurTab($tab){
		
		$this->assign('curTab',$tab);
		return $this;
	} 
	
    public function user(){
    	$user = D('User')->getUserList(10);
    	$this->assign('user',$user);
    	$this->setCurTab('User');
    	$this->display();
    }
    public function group(){
    	$this->setCurTab('Group');
    	$this->display();
    }
    public function verify(){
    	$this->setCurTab('Verify');
    	$this->display();
    }
    
    //添加用户
    public function addUser(){
    	$this->setCurTab('AddUser');
    	$this->display();
    }
    //转移用户组
    public function moveGroup($uid){
    	$gids= D('RoleUser')->getUserGroup($uid);
    	$groups = M('Role')->field('id,name')->where(array('status'=>1))->select();
    	$this->assign('groups',$groups);
    	$this->assign('gids',$gids);
    	//dump($groups);
    	$this->display();
    }
    public function doMoveUserGroup(){
    	$return = array('status'=>'0','info'=>L('PUBLIC_ADMIN_OPRETING_ERROR'));
    	if(!empty($_POST['uid']) && !empty($_POST['group_ids'])){
    		if($res = D('RoleUser')->resetUserGroup($_POST['uid'],$_POST['group_ids'])){
    			$return = array('status'=>1,'data'=>L('PUBLIC_ADMIN_OPRETING_SUCCESS'));
    			//TODO 记录日志
    		}else{
    			//$return['info'] = D('UserRole')->getError();
    		}
    	}
    	echo json_encode($return);exit();
    	
    }
    
}