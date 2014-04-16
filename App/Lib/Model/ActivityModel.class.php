<?php
/**
 * 活动模型 - 业务对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class ActivityModel extends Model {
	private $_uid = null; // 登录的用户ID
	private $_error = null; // 错误信息
	
	/**
	 * 初始化方法，设置默认用户信息
	 * @return void
	 */
	public function _initialize() {
		$this->_uid = intval($_SESSION[C('USER_AUTH_KEY')]);
	}
	
	/**
	 * 设置用户ID
	 */
	public function setUid($uid){
		$this->_uid = intval($uid);
	}
	
	/**
	 * 获取登录用户创建的所有已发布活动信息
	 *@return 返回登录用户创建的所有已发布活动信息
	 */
	public function getLoginUserActivityInfo(){
		$uid = $this->_uid;
		if(empty($uid)){
			$this->_error = L('PUBLIC_USER_ID_NO_EXIST');  //用户ID不存在
		}
		$where['uid'] = $uid;
		$where['is_post'] = 1;
		$info = $this->where($where)->select();
		if(!$info){
			$this->_error = L('PUBLIC_LOGIN_USER_NO_CREATE_ACTIVITY'); //登录用户没有创建活动
			return false;
		}
		return $info;
	}
	
	/**
	 * 获取登录用户创建的所有正在进行的活动信息
	 *@return 返回登录用户创建的所有正在进行的活动信息
	 */
	public function getLoginUserUnderwayActivityInfo(){
		$uid = $this->_uid;
		if(empty($uid)){
			$this->_error = L('PUBLIC_USER_ID_NO_EXIST');  //用户ID不存在
		}
		$where['uid'] = $uid;
		$where['is_post'] = 1;
		$where['is_complete'] = 0;
		$info = $this->where($where)->select();
		if(!$info){
			$this->_error = L('PUBLIC_LOGIN_USER_NO_CREATE_ACTIVITY'); //登录用户没有创建活动
			return false;
		}
		return $info;
	}
	
	/**
	 * 获取登录用户创建的所有已完成的活动信息
	 *@return 返回登录用户创建的所有已完成的活动信息
	 */
	public function getLoginUserCompleteActivityInfo(){
		$uid = $this->_uid;
		if(empty($uid)){
			$this->_error = L('PUBLIC_USER_ID_NO_EXIST');  //用户ID不存在
		}
		$where['uid'] = $uid;
		$where['is_complete'] = 1;
		$info = $this->where($where)->select();
		if(!$info){
			$this->_error = L('PUBLIC_LOGIN_USER_NO_CREATE_ACTIVITY'); //登录用户没有创建活动
			return false;
		}
		return $info;
	}
	
	/**
	 * 获取所有已发布活动信息
	 *@return 返回所有已发布活动的信息
	 */
	public function getAllActivityInfo(){
		$where['is_post'] = 1;
		$info = $this->where($where)->select();
		if(!$info){
			$this->_error = L('PUBLIC_NO_CREATE_ACTIVITY'); // 没有人创建过活动
			return false;
		}
		return $info;
	}
	
	/**
	 * 通过activityID获取已发布活动信息
	 * @param $activityId 活动专题的ID
	 * @return 返回activityId的已发布活动专题信息
	 */
	public function getActivityById($activityId){
		if(empty($activityId)){
			$this->_error = L('PUBLIC_ACTIVITY_ID_EMPTY'); //活动ID是空的
			return false;
		}
		$activityId = intval($activityId);
		$where['id'] = $activityId;
		$where['is_post'] = 1;
		$info = $this->where($where)->find();
		if(!$info){
			$this->_error = L('PUBLIC_ACTIVITY_NO_EXIST'); //该活动相关信息不存在
			return false;
		}
		return $info;
	}
	
	/**
	 * 获取用户创建的所有已发布活动信息
	 *@param $user_id 用户的ID
	 *@return 返回用户创建的所有已发布活动信息
	 */
	public function getActivityByUserId($user_id){
		$uid = $user_id;
		if(empty($uid)){
			$this->_error = L('PUBLIC_USER_ID_NO_EXIST');  //用户ID不存在
		}
		$where['uid'] = $uid;
		$where['is_post'] = 1;
		$info = $this->where($where)->select();
		if(!$info){
			$this->_error = L('PUBLIC_USER_NO_CREATE_ACTIVITY'); //用户没有创建活动
			return false;
		}
		return $info;
	}
	
	/**
	 * 通过activityID获取活动海报信息
	 * @param $activityId 活动专题的ID
	 * @return 返回二维数组的activityId的活动专题中的海报信息
	 */
	public function getActivityPosterById($activityId){
		$info = $this->getActivityById($activityId);
		$posters = explode(',',preg_replace('/[，,]+/u', ',', $info['poster']));
		$attachs = D('Attach')->getAttachByIds($posters);
		if(!$attachs){
			$this->_error = L('PUBLIC_ACTIVITY_POSTER_NO_EXIST'); //活动海报不存在
			return false;
		}
		return $attachs;
	}
	
	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}
?>