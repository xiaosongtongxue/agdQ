<?php
/**
 * 用户统计模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class UserCountModel extends Model {
	protected $_user_data_model ; 	//用户统计数据模型

	/**
	 * 初始化方法，初始化默认参数
	 * @return void
	 */
	public function _initialize() {
		$this->_user_data_model = D('UserData');
	}

	/**
     * 获取指定用户的通知统计数目
     * @param integer $uid 用户UID
     * @return array 指定用户的通知统计数目
     */
	public function getUnreadCount($uid = '') {
		if(empty($uid)){
			$uid = $_SESSION[C('USER_AUTH_KEY')];
		}
		$user_data = $this->_user_data_model->setUid($uid)->getUserData();
		/*
		+------------------------------------------------------------
			TODO:未读通知数目-->Nodify
		+------------------------------------------------------------
		*/
		// 未读通知数目
		//$return['unread_notify']  = model('Notify')->getUnreadCount($uid);
		//未读@Me数目
		$return['unread_atme'] = intval($user_data['unread_atme']);
		//未读评论数目
		$return['unread_comment'] = intval($user_data['unread_comment']);

		/*
		+------------------------------------------------------------
			TODO:未读短信息数目 or 新的关注数目 or
		+------------------------------------------------------------
		*/

		//合计的未读数目
		$return['unread_total'] = array_sum($return);

		//清除值为0的元素
		foreach($return as $k => $v){
			if($v==0){
				unset($return[$k]);
			}
		}

		return $return;
	}

	/**
	 * 更新指定用户的通知统计数目
	 * @param integer $uid 用户UID
	 * @param string $key 统计数目的Key值
	 * @param integer $nums 数目变动的值
	 * @return void
	 */
	public function updateUserCount($uid, $key, $nums) {
		$this->_user_data_model->setUid($uid)->updateKey($key, $nums);
	}

	/**
	 * 重置指定用户的通知统计数目
	 * @param integer $uid 用户UID
	 * @param string $key 统计数目的Key值
	 * @param integer $value 统计数目变化的值，默认为0
	 * @return void
	 */
	public function resetUserCount($uid, $key, $value = 0) {
		$this->_user_data_model->setKeyValue($uid, $key, $value);
	}
}

?>
