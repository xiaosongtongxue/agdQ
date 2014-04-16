<?php
// 后台用户管理模块
class RBACAction extends AdministratorAction {
	protected $_tab = array(
		'User'		=>array('name'=>'User','title'=>'用户管理','href'=>'__URL__/user'),
		'Group'		=>array('name'=>'Group','title'=>'用户组管理','href'=>'__URL__/group'),
		'Verify'	=>array('name'=>'Verify','title'=>'用户认证','href'=>'__URL__/verify'),
		//'AddUser'	=>array('name'=>'AddUser','title'=>'添加用户','href'=>'__URL__/addUser'),
		'AddNode'	=>array('name'=>'AddNode','title'=>'添加节点','href'=>'__URL__/addNode'),
	);
	public function _initialize(){
		parent::_initialize();
		$this->setCurMenu('RBAC');
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

    //添加用户
    public function addUser(){
    	//$this->setCurTab('AddUser');
    	$this->display();
    }
    public function group(){
    	$this->setCurTab('Group');
    	$group = D('Role')->getRoleList();
    	$this->assign('group',$group);
    	$this->display();
    }
    public function addGroup(){
    	$this->display();
    }
    public function doAddGroup(){
    	D('Role')->addRole($_POST);
    	redirect(U('group'));
    }
    public function verify(){
    	$this->setCurTab('Verify');
    	$this->display();
    }
   
    //转移用户组
    public function moveGroup($uid){
    	$gids= D('RoleUser')->getUserGroup($uid);
    	$groups = M('Role')->field('id,name')->where(array('status'=>1))->select();
    	$this->assign('uid',$uid);
    	$this->assign('groups',$groups);
    	$this->assign('gids',$gids);
    	//dump($groups);
    	$this->display();
    }
    public function doMoveGroup(){
    	$return = array('status'=>'0','info'=>L('PUBLIC_ADMIN_OPRETING_ERROR'));
    	if(!empty($_POST['uid']) && !empty($_POST['group_ids'])){
    		if($res = D('RoleUser')->resetUserGroup($_POST['uid'],$_POST['group_ids'])){
    			$return = array('status'=>1,'info'=>'操作成功');
    			//TODO 记录日志
    		}else{
    			//$return['info'] = D('UserRole')->getError();
    		}
    	}
    	echo json_encode($return);exit();
    	
    }
    
    public function addNode(){
    	$this->setCurTab('AddNode');
    	$this->display();
    }
    
    public function test(){
    	dump(D('Module')->fetch_module());
    }
    
}