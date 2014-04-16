<?php
// 角色模型
class RoleModel extends CommonModel {
	/*
	 * 删除角色
	 * @param $role_id 角色ID
	 * @return true或者false
	 */
    public function delRole($role_id){
    	$this->where(array('id'=>$role_id))->delete();
    	//删除相关数据
    	M('RoleUser')->where(array('role_id'=>$role_id))->delete();
    	M('Access')->where(array('role_id'=>$role_id))->delete();
    	return true;
    }
    public function addRole($add = array()){
    	$add['ctime'] = time();
    	$add['pid'] = 0;
    	$add['status'] = 1;
    	$this->data($add)->add();
    }
    public function getRoleList(){
    	$map['status'] = 1;
    	$list = $this->where($map)->select();
    	//$list['ctime'] = date('Y-m-d',$list['ctime']);
    	return $list;
    }
    
}