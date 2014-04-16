<?php
/**
 * 圈子模型 - 业务对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class CircleModel extends Model {
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
	 * 添加到基本圈子
	 *@param array $info圈子信息
	 *@return 返回数据库处理结果
	 */
	public function addBaseCircle($info){
		$info['uid']                = $this->_uid;
		
		$result = D('UserCircle')->addUserCircle($info);
		if($result){
			return $result;
		}else{
			$this->_error = L('CIRCLE_CREATE_FAIL'); //创建圈子失败
			return false;
		}
	}
	
	/**
	 * 添加到分类圈子
	 *@param array $info圈子的分支信息
	 *@return 返回数据库处理结果
	 */
	public function addCircle($info){
		$circle['cid']       = isset($info['cid']) ? $info['cid'] : 0;
		$circle['admin_id']  = isset($info['admin_id']) ? $info['admin_id'] : intval($_SESSION[C('USER_AUTH_KEY')]);
		$circle['phone']     = t($info['phone']);
		$circle['qq']        = t($info['qq']);
		$result = $this->add($circle);
		
		if($result){
			return $result;
		}else{
			$this->_error = L('CIRCLE_CREATE_BRANCH_FAIL'); //创建分支圈子失败
			return false;
		}
	}
		
	/**
	 * 添加班级圈
	 */
	public function addClassCircle(){
		$add['user_circle_name']   = "校圈";
		$add['uid']                = 0;// 班级圈无需创建者
		$add['user_circle_intro']  = "将啊哈哈大家速度哈师大";
		$add['user_circle_icon']   = 'circle.jpg';// 默认图标
		$add['user_circle_type']   = 2;//2为班级圈类型
		$add['app_name']           = 'public' ;
		$add['is_authenticate']    = 0;
		D('UserCircle')->addUserCircle($add);
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