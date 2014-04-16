<?php
/**
 * 用户统计数据模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class UserDataModel extends Model {

	protected $tableName = 'user_data';
	protected $fields = array(
					0=>'id',	//主键ID
					1=>'uid',	//户用UID
					2=>'key',
					3=>'value',	//对应Key的值
					4=>'mtime',	//当前时间戳
					'_pk'=>'id',
					);
	protected $uid = '';	//用户ID
	protected $_error = null ;   //错误信息字段

	/**
	 * 初始化方法，设置默认用户信息
	 * @return void
	 */
	public function _initialize() {
		$this->uid = $_SESSION[C('USER_AUTH_KEY')];
	}

	/**
	 * 设置用户UID
	 * @param integer $uid 用户UID
	 * @return object 用户统计数据对象
	 */
	public function setUid($uid) {
		$this->uid = $uid;
		return $this;
	}

	/**
	 * 更新某个用户的指定Key值的统计数目
	 * Key值：
	 * unread_comment：评论未读数
	 * unread_atme：@Me未读数
	 * @param string $key Key值
	 * @param integer $nums 更新的数目
	 * @param boolean $add 是否添加数目，默认为true
	 * @param integer $uid 用户UID
	 * @return array 返回更新后的数据
	 */
	public function updateKey($key, $nums, $add = true,$uid = ''){
		if($nums == 0){
			$this->_error = L('PUBLIC_MODIFY_NO_REQUIRED');		//不需要修改
			return false;
		}

		//如果数目小于0，则认为是减少数目
		$nums < 0 && $add = false;
		$key = t($key);

		//获取当前用户设置的统计的数目
		$data = $this->getUserData($uid);
		//if(empty($data) || !$data){
		if(empty($data[$key]) || !$data[$key]){
			$data = array();
			$data[$key] = $nums;
		}else{
			$data[$key] = $add ? ($data[$key] + abs($nums)) : ($data[$key] - abs($nums));
		}
		$data[$key] < 0 && $data[$key] = 0;

		$where['uid'] = empty($uid) ? $this->uid : $uid;
		$where['key'] = $key;
		$this->where($where)->limit(1)->delete();
		$where['value'] = $data[$key];
		$where['mtime'] = date('Y-m-d H:i:s');
		$this->add($where);

		return $data;
	}

	/**
	 * 设置指定用户指定Key值的统计数目
	 * @param integer $uid 用户UID
	 * @param string $key Key值
	 * @param integer $value 设置的统计数值
	 * @return void
	 */
	public function setKeyValue($uid, $key, $value){
		$where['uid'] = $uid;
		$where['key'] = $key;
		$this->where($where)->delete();
		$where['value'] = $value;
		$where['mtime'] = date('Y-m-d H:i:s');
		$this->add($where);
	}

	/**
	 * 获取指定用户的统计数据
	 * @param integer $uid 用户UID
	 * @return array 指定用户的统计数据
	 */
	public function getUserData($uid = ''){
		if(empty($uid)){
			$uid = $this->uid;
		}

		$where['uid'] = $uid;
		$data = array();
		$list = $this->where($where)->select();
		if(!empty($list)){
			foreach($list as $v){
				$data[$v['key']] = (int)$v['value'];
			}
		}

		return $data;
	}

	/**
	 * 批量获取多个用户的统计数目
	 * @param array $uids 用户UID数组
	 * @return array 多个用户的统计数目
	 */
	public function getUserDataByUids($uids){
		$data = array();
		foreach($uids as $v){
			$data[$v] = $this->getUserData($v);
		}
		return $data;
	}

	/**
	 * 返回最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}
?>