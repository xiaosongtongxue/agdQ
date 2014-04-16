<?php
/**
 * 用户圈模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class UserCircleModel extends Model {
	
	private $_error = null;    // 错误信息
/*	protected $tableName = 'user_circle';
	protected $fields = array(
							0=>'user_circle_id',
							1=>'user_circle_name',
							2=>'user_circle_intro',
							3=>'uid',
							4=>'ctime',
							5=>'user_circle_icon',
							6=>'user_circle_type',
							7=>'app_name',
							8=>'is_authenticate'
						);*/

	/**
	 * 添加用户圈信息
	 * @param array $add 相关用户圈信息
	 * @return integer 相关用户圈ID
	 * @example $add['user_circle_name']   = "校圈";
				$add['uid']                = $uid;
				$add['user_circle_intro']  = "将啊哈哈大家速度哈师大";
				$add['user_circle_icon']   = 'circle.jpg' ;
				$add['user_circle_type']   = 0;
				$add['app_name']           = 'public' ;
				$add['is_authenticate']    = 0;
				D('UserCircle')->addUserCircle($add);
	 */
	public function addUserCircle($add){
		if(!empty($add['user_circle_name'])){
			// 判断用户圈名称是否唯一
			if(!$this->isUserCircleNameExist($add['user_circle_name'])){
				$data['user_circle_name'] = t($add['user_circle_name']);
			}else{
				$this->_error = L('PUBLIC_USER_CIRCLE_NAME_EXIST');  //用户圈名称已存在
				return false;
			}
		}else{
			$this->_error = L('PUBLIC_USER_CIRCLE_NAME_NO_NULL');	// 用户圈名称不能为空
			return false;
		}
		!empty($add['user_circle_intro']) && $data['user_circle_intro'] = t($add['user_circle_intro']);
		$data['uid']              = isset($add['uid']) ? intval($add['uid']) : 0;
		$data['ctime']            = time();
		$data['user_circle_icon'] = !empty($add['user_circle_icon']) ? t($add['user_circle_icon']) : 'circle.jpg' ;
		$data['user_circle_type'] = isset($add['user_circle_type']) ? intval($add['user_circle_type']) : 0 ;
		$data['app_name']         = !empty($add['app_name']) ? t($add['app_name']) : 'public' ;
		$data['is_authenticate']  = isset($add['is_authenticate']) ? intval($add['is_authenticate']) : 0 ;
		
		//添加用户圈
		$res = $this->add($data);
		//清除相关缓存
		$this->cleanCache();
		
		return $res;
	}
	
	/**
	 * 更新用户圈信息
	 * @param array $update 相关用户圈信息
	 * @return integer 相关用户圈ID
	 */
	public function updateUserCircle($update){
		//where[]
		!empty($update['user_circle_id']) && $where['user_circle_id'] = intval(t($update['user_circle_id']));
		//data[]
		if(!empty($update['user_circle_name'])){
			if(!$this->isUserCircleNameExist($update['user_circle_name'])){
				$data['user_circle_name'] = t($update['user_circle_name']);
			}else{
				$this->_error = L('PUBLIC_USER_CIRCLE_NAME_EXIST');  //用户圈名称已存在
				return false;
			}
		}
		
		!empty($update['user_circle_icon']) && $data['user_circle_icon'] = t($update['user_circle_icon']);
		isset( $update['user_circle_type']) && $data['user_circle_type'] = intval($update['user_circle_type']);
		isset( $update['is_authenticate'])  && $data['is_authenticate']  = intval($update['is_authenticate']);
		
		//更新用户圈
		$res = $this->where($where)->save($data);
		//清除相关缓存
		$this->cleanCache();
		
		return $res;
	}

	/**
	 * 删除指定的用户圈
	 * @param integer or array $cids 用户圈ID
	 * @return boolean 是否删除成功
	 */
	public function delUserCircles($cids) {
		// 验证数据
		if(empty($cids)) {
			$this->_error = L('PUBLIC_USER_CIRCLE_ID_NULL');			// 用户圈ID是空的
			return false;
		}
		// 系统默认的用户圈不能进行删除
		/**
		 +----------------------------------------------------------------------------------
		 	//TODO:有哪些系统默认的用户圈是不能删除的代码
		 *
		 +----------------------------------------------------------------------------------
		 */
		
		// 删除指定用户圈
		$where = array();
		$where['user_circle_id'] = is_array($cids) ? array('IN', $cids) : intval($cids);
		if($this->where($where)->delete()) {
			// TODO:后续操作==================================================================================================
			M('user_circle_link')->where('user_circle_id='.$cids)->delete();  //删除用户关联
			$this->cleanCache();
			return true;
		}

		return false;
	}

	/**
	 * 返回一个用户圈信息
	 * @param integer $cid 用户圈ID
	 * @return array 用户圈信息
	 */
	public function getUserCircle($cid){
		if(empty($cid)){
			$this->_error = L('PUBLIC_USER_CIRCLE_ID_NULL');			// 用户圈ID是空的
			return false;
		}
		
		if(($data = D('Cache')->get('AllUserCircle')) == false) {
			$list = $this->select();
			foreach($list as $k => $v) {
				$v['user_circle_icon'] = $this->getUserCircleIcon($v['user_circle_icon']);
				$data[$v['user_circle_id']] = $v;
			}
			D('Cache')->set('AllUserCircle', $data);
		}
		
		return $data[$cid];
	}
	
	/**
	 * 返回多个指定用户圈信息
	 * @param array $cids 用户圈ID
	 * @return array 用户圈信息
	 */
	public function getUserCircles($cids){
		if(empty($cids)){
			$this->_error = L('PUBLIC_USER_CIRCLE_ID_NULL');			// 用户圈ID是空的
			return false;
		}
		
		if(($data = D('Cache')->get('AllUserCircle')) == false) {
			$list = $this->select();
			foreach($list as $k => $v) {
				$v['user_circle_icon'] = $this->getUserCircleIcon($v['user_circle_icon']);
				$data[$v['user_circle_id']] = $v;
			}
			D('Cache')->set('AllUserCircle', $data);
		}
		
		// 返回指定的用户圈
		if(is_array($cids)){
			$r = array();
			foreach($cids as $v){
				$r[$v] = $data[$v];
			}
			return $r;
		} else {
			return $data[$cids];
		}
	}
	
	/**
	 * 返回一个类型的用户圈信息
	 * @param integer $user_circle_type 用户圈类型
	 * @return array 用户圈信息
	 */
	public function getUserCircleByType($user_circle_type){
		if(empty($user_circle_type)){
			$this->_error = L('PUBLIC_USER_CIRCLE_TYPE_NULL');			// 用户圈的类型是空的
			return false;
		}
		
		if(($data = D('Cache')->get('AllUserCircle')) == false) {
			$where['user_circle_type'] = $user_circle_type;
			$list = $this->where($where)->select();
			foreach($list as $k => $v) {
				$v['user_circle_icon'] = $this->getUserCircleIcon($v['user_circle_icon']);
				$data[$v['user_circle_id']] = $v;
			}
			D('Cache')->set('AllUserCircle', $data);
		}
		
		return $data;
	}
	
	/**
	 * 返回所有用户圈
	 * @return array 用户圈信息
	 */
	public function getAllUserCircle(){
		if(($data = D('Cache')->get('AllUserCircle')) == false) {
			$list = $this->select();
			foreach($list as $k => $v) {
				$v['user_circle_icon'] = $this->getUserCircleIcon($v['user_circle_icon']);
				$data[$v['user_circle_id']] = $v;
			}
			D('Cache')->set('AllUserCircle', $data);
		}
		
		return $data;
	}
	
	/**
	 * 获取用户圈的Hash数组
	 * @param string $k Hash数组的Key值字段
	 * @param string $v Hash数组的Value值字段
	 * @return array 用户圈的Hash数组
	 */
	public function getHashUserCircle($k = 'user_circle_id', $v = 'user_circle_name') {
	    $list = $this->getAllUserCircle();
	    $r = array();
	    foreach($list as $lv) {
	    	$r[$lv[$k]] = $lv[$v];
	    }

	    return $r;
    }
	
	/**
	 * 判断用户圈名称是否唯一
	 * @param string $user_circle_name 用户圈名称
	 * @return boolean 
	 */
	public function isUserCircleNameExist($user_circle_name){
		/*$where['user_circle_name'] = $user_circle_name;
		$res = $this->where($where)->find();
		return $res;*/
		// 无需验证是否唯一 ===========================================
		return false;
	}
	
	/**
	 * 判断用户是否是该圈的创建者
	 * @param inteter $uid
	 * @param integer $cid
	 * @return boolean 是否是创建者
	 */
	public function isAdmin($uid , $cid){
		if(!isset($uid)){
			return false;
		}
		
		if(!isset($cid)){
			return false;
		}
		$where['uid'] = $uid;
		$where['user_circle_id'] = $cid;
		
		$res = $this->where($where)->find();
		return $res;
	}
	
	/**
	 * 获取用户圈的创建者
	 * @param integer $cid
	 * @return array 创建者的信息
	 */
	public function getCircleAdminInfo($cid){
		if(!isset($cid)){
			return false;
		}
		
		$where['user_circle_id'] = $cid;
		$res = $this->where($where)->find();
		return $res ? D('User')->getUserInfo($res['uid']) : false;
	}
	
	/**
     * 获取指定用户圈的创建者
     * @param int $cid 用户圈ID
     * @return array  返回用户圈创建者的信息
     */
	public function getCircleCreater($cid){
		if(!isset($cid)){
			return false;
		}
		$where['user_circle_id'] = $cid;
		$uid = $this->where($where)->getField('uid');
		
		$userData = D('User')->getUserInfo($uid);
		return $userData;
	}
	
	/**
     * 获取指定用户圈的图标
     * @param int $cid 用户圈ID
     * @return string  返回用户圈图标的img标签
     */
    public function getUserCircleIcon($user_circle_icon) {
		if(ereg("^[0-9]+$",$user_circle_icon)){
			$data = D('Attach')->getAttachById($user_circle_icon);
			return ATTACH_URL.'/'.$data['save_path'].$data['save_name'];
		}else{
			return IMAGE_URL.'/'.$user_circle_icon;
		}
    }
	
	/**
     * 清除用户圈缓存
     * @return void
     */
	public function cleanCache() {
		D('Cache')->rm('AllUserCircle');
	}
	
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}