<?php
class RoleUserModel extends Model{
	/*
	 * 添加用户到角色
	 * @param integer $uid 用户ID 
	 * @param integer $gid 角色ID 
	 * @return boolean 返回true或者返回false
	 */
	public function setUserToRole($uid,$gid){
		//uid,gid有效性检查
		$isValidUid = M('User')->where(array('uid'=>$uid,'is_allow'=>1))->count();
		$isValidGid = M('Role')->where(array('id'=>$gid,'status'=>1))->count();
		if($isValidUid + $isValidGid == 0){
			$this->error = '操作失败,用户或角色不可用';
			return false;
		}
		//检查是否已经操作过
		$count = $this->where(array('user_id'=>$uid,'role_id'=>$gid))->count();
		if($count != 0) {
			$this->error = '操作失败,用户已经属于该角色';
			return false;
		}
		//添加用户到角色
		$add['role_id'] = $gid;
		$add['user_id'] = $uid;
		$this->data($add)->add();
		return true;
			
	}
	/*
	 * 删除用户的某一角色
	* @param integer $uid 用户ID
	* @param integer $gid 角色ID
	* @return boolean 返回true或者返回false
	*/
	public function delUserFromRole($uid,$gid){
		//uid,gid有效性检查
		$isValidUid = M('User')->where(array('uid'=>$uid,'is_allow'=>1))->count();
		$isValidGid = M('Role')->where(array('id'=>$gid,'status'=>1))->count();
		if($isValidUid + $isValidGid == 0){
			$this->error = '操作失败,用户或角色不可用';
			return false;
		}
		//检查是否已经操作过
		$count = $this->where(array('user_id'=>$uid,'role_id'=>$gid))->count();
		if($count != 0) {
			$this->error = '操作失败,用户已经属于该角色';
			return false;
		}
		//添加用户到角色
		$del['role_id'] = $gid;
		$del['user_id'] = $uid;
		$this->where($del)->delete();
		return true;
	}
	
	public function getUserGroup($uid = ''){
		if(empty($uid)){
			$uid = $_SESSION[C(USER_AUTH_KEY)];
		}
		//$groups = array();
		$rs = $this->field('role_id')->where(array('user_id'=>$uid))->select();
// 		foreach($rs as $group){
// 			$groupName = M('Role')->where(array('id'=>$group['role_id']))->getField('name');
// 			$group[] = array('role_id'=>$group['role_id'],'role_name'=>$groupName); 
// 		}
		$groups = getSubByKey($rs,'role_id');
		return $groups;
	}
	/**
	 * 转移用户的用户组
	 * @param string $uid 用户UID，多个用“,”分割
	 * @param string $user_group_id 用户组ID，多个用“,”分割
	 * @return boolean 是否转移成功
	 */
	public function resetUserGroup($uids,$group_ids){
		// 验证数据
		if(empty($uids) && empty($group_ids)) {
			$this->error = L('PUBLIC_USER_GROUP_EMPTY');			// 用户组或用户不能为空
			return false;
		}
		$uids = explode(',', $uids);
		$group_ids = explode(',', $group_ids);
		$uids = array_unique(array_filter($uids));
		$group_ids = array_unique(array_filter($group_ids));
		if(!$uids || !$group_ids) {
			return false;
		}
		$map['user_id'] = array('IN', $uids);
		$this->where($map)->delete();
		foreach($uids as $v) {
			$save = array();
			$save['user_id'] = $v;
			foreach($group_ids as $gv){
				$save['role_id'] = $gv;
				$this->add($save);
			}
		}
		//model('User')->cleanCache($uids);
	
		return true;
	}
	
}
?>