<?php
/**
 * 用户模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class UserModel extends Model {
	protected $_link = array(
		'Circle'=>array(
			'mapping_type'   		=>MANY_TO_MANY,
			'class_name'    		=>'Circle',
			'mapping_name'			=>'Circle',
			'foreign_key'			=>'uid',
			'relation_foreign_key'	=>'cid',
			'relation_table'		=>'CircleUser',
			//'condition'			=>'',

		 ),
	);
	protected $_validate = array(
			//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
			//必须	
			array('nickname','require','昵称必须!'), //默认情况下用正则进行验证
			array('stu_id','require','学号必须!'), //默认情况下用正则进行验证
			array('email','require','邮箱必须!'), //默认情况下用正则进行验证
			array('password','require','密码必须!'), //默认情况下用正则进行验证
			//唯一
			array('nickname','','昵称不可用!',0,'unique'), // 验证nickname字段是否唯一
			array('stu_id','','学号已被注册',0,'unique'), // 验证stu_id字段是否唯一
			array('email','','邮箱已被注册',0,'unique'), // 验证email字段是否唯一
			//其他
			array('email','email','email格式不正确!'),
			
			//array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
			//array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
			
	);
	protected  $_auto =	array(
			//array(填充字段,填充内容,[填充条件,附加规则])
			array('is_allow','1'),  // 新增的时候把status字段设置为1
			array('password','md5',self::MODEL_BOTH,'function'),
			array('reg_time','time',self::MODEL_INSERT,'function'),
			array('reg_ip','get_client_ip',self::MODEL_INSERT,'function'),
			array('login_ip','get_client_ip',self::MODEL_UPDATE,'function'),
			//array('update_time','time',self::MODEL_UPDATE,'function'),
	);
	
//	protected $tableName = 'user';

/*	protected $fields = array (
			0 => 'uid',
			1 => 'nickname',
			2 => 'stu_id',
			3 => 'sex',
			4 => 'email',
			5 => 'pwd',
			6 => 'is_audit',
			7 => 'is_active',
			8 => 'reg_time',
			9 => 'reg_ip',
			10 => 'power',
			11 => 'login_ip',
			12 => 'last_login_time',
			13 => 'blacklist',
			14 => 'is_del',
			15 => 'about',
			16 => 'search_key',
			'_autoinc' => true,
			'_pk' => 'uid'
	);*/
	//字段过滤
	private function _filter($map = array()){
		isset($map['uid']) && $return['uid'] = intval($map['uid']);
		isset($map['stu_id']) && $return['stu_id'] = intval($map['stu_id']);
		
		if(isset($map['email'])){
			if(!isValidEmail($map['email']))
				return false;	
			else{
				$return['email'] = $map['email'];
			}
		}
		return $return;
	}
	/*
	 * 删除单个用户,只能通过uid,stu_id,email三种方式
	 * @param array $map 查询条件
	 * @return boolean true或者false 
	 */
	public function delUser($map = array()){
		
		isset($map['uid']) && $m['uid'] = intval($map['uid']);
		isset($map['stu_id']) && $m['stu_id'] = intval($map['stu_id']);
		isset($map['email']) && $m['email'] = $map['email'];
		
		$info = $this->field('uid','email','stu_id')->where($m)->find();
		$uid = $info['uid'];
		$stu_id = $info['stu_id'];
		if(empty($info)) {
			$this->error = '删除失败,该用户不存在!';
			return false;
		}
		//任何人都不能删除超级管理员
		if(in_array($stu_id,C(SUPER_ADMIN))){
			$this->error = '你没有权限!';
			return false;
		}
		
		$this->where($m)->limit(1)->delete();
		//删除关联数据
		M('RoleUser')->where(array('user_id'=>$uid))->delete();
		M('CircleUser')->where(array('uid'=>$uid))->delete();
		M('Collection')->where(array('uid'=>$uid))->delete();
		return true;
	}
	/*
	 * @param array $info 用户信息
	 * @return max 返回用户ID或者返回false
	 */
	public function addUser($info = array()){
		if($this->create($info)){
			$uid = $this->add();
			return $uid;
		}
		return false;
	}
	
	/**
	 * 获取用户列表，后台可以根据用户组查询
	 * @param integer $limit 	结果集数目，默认为20
	 * @param array $where 	查询条件
	 * @return array 用户列表信息
	 */
	public function getUserList($limit = 20, $where = array(), $order = "uid DESC"){
		//添加用户表的查询，用于关联查询

		if(isset( $_POST)){
			$_POST['uid'] && $where['uid'] = intval ($_POST['uid']);
			$_POST['nickname'] && $where['nickname'] = t($_POST['nickname']);
			$_POST['email'] && $where['email'] = t($_POST['email']);
			isset($_POST['is_audit']) && $where['is_audit'] = intval($_POST['is_audit']);
			!empty($_POST['sex']) && $where['sex'] = intval($_POST['sex']);

			/*
			+-------------------------------------------------------------------------------
				TODO:注册时间判断
			+-------------------------------------------------------------------------------
			*/

			/*
			+-------------------------------------------------------------------------------
				TODO:用户组信息过滤
			+-------------------------------------------------------------------------------
			*/

			/*
			+-------------------------------------------------------------------------------
				TODO:用户标签过滤
			+-------------------------------------------------------------------------------
			*/
		}

		// 查询数据
		$list = $this->where($where)->order($order)->limit($limit)->select();

		/*
		+-------------------------------------------------------------------------------
			TODO:数据组装
		+-------------------------------------------------------------------------------
		*/

		return $list;
	}

	/**
	 * 获取用户列表信息 - 未分页型
	 * @param array $where  	查询条件
	 * @param integer $limit   	结果集数目，默认为20
	 * @param string $field   	需要显示的字段，多个字段之间使用","分割，默认显示全部
	 * @param string $order 	排序条件，默认uid DESC
	 * @return array 用户列表信息
	 */
	public function getList($where = array(), $limit = 20, $field = '*', $order = 'uid DESC'){
		$list = $this->where($where)->limit($limit)->field($field)->order($order)->select();
		return $list;
	}

	/**
	 * 获取用户列表信息 - 分页型
	 * @param array $where   	查询条件
	 * @param integer $limit  	结果集数目，默认为20
	 * @param string $field   	需要显示的字段，多个字段之间使用","分割，默认显示全部
	 * @param string $order    	排序条件，默认uid DESC
	 * @return array 用户列表信息
	 */
	public function getPage($where = array(), $limit = 20, $field = '*', $order = 'uid DESC') {
		/*
		+--------------------------------------------------------------------
			TODO:现在不了解分页型是如何形式
		+--------------------------------------------------------------------
		*/
	}

	/**
	 * 获取指定用户的相关信息
	 * @param integer $uid   	用户UID
	 * @return array 指定用户的相关信息
	 */
	public function getUserInfo($uid, $field = '*'){
		$uid = intval($uid);
		if($uid < 0){
			$this->_error = L('PUBLIC_UID_INDEX_ILLEAGEL');	//UID参数值不合法
			return false;
		}
		$where['uid'] = $uid;

		$user = $this->_getUserInfo($where, $field);
		
		return $user;
	}
	
	/**
	 * 为@搜索提供用户信息
	 * @param integer $uid  	用户UID
	 * @return array 指定用户的相关信息
	 */
	public function getUserInfoForSearch($uid, $field){
		$uid = intval($uid);
		if($uid < 0){
			$this->_error = L('PUBLIC_UID_INDEX_ILLEAGEL');	//UID参数值不合法
			return false;
		}
		$where['uid'] = $uid;

		$user = $this->_getUserInfo($where, $field);

		return $user;
	}
	
	/**
	 * 获取指定用户的相关信息
	 * @param array $where	查询条件
	 * @return array 指定用户的相关信息
	 */
	private function _getUserInfo($where, $field = '*'){
		$user = $this->field($field)->where($where)->find();
		unset($user['pwd']);//====================================注意User表里面只有pwd字段，在别的地方看到就要修改
		
		if (! $user) {
			$this->error = L ( 'PUBLIC_GET_INFORMATION_FAIL' );      // 获取用户信息失败
			return false;
		} else {
			$uid = $user['uid'];
			/*
			 +--------------------------------------------------------------------
			   TODO:头像照片路径，头像路径，个人资料路径等等用户相关的信息
			 +--------------------------------------------------------------------
			*/
			// 添加$uid用户的头像路径
			$avatar_url = D('Avatar')->init($uid)->getUserAvatar();
			$user = array_merge($user, $avatar_url);
			
			return $user;
		}
	}

	/**
	 * 获取q_user表的数据，带缓存功能
	 * @param array $where 	查询条件
	 * @return array 指定用户的相关信息
	 */
	 /*
	public function getUserData($where, $field = '*'){
		$user = $this->where($where)->field($field)->find();
		unset($user['pwd']);

		return $user;
	}*/


	/**
	 * 通过用户昵称查询用户相关信息
	 * @param string $uname 昵称信息
	 * @return array 指定昵称用户的相关信息
	 */
	public function getUserInfoByName($uname , $where) {
		if(empty($uname)) {
			$this->error = L('PUBLIC_USER_EMPTY');			// 用户名不能为空
			return false;
		}
		$where['uname'] = t($uname);
		$data = $this->_getUserInfo($where);
		return $data;
	}

	/**
	 * 通过邮箱查询用户相关信息
	 * @param string $email 用户邮箱
	 * @return array 指定昵称用户的相关信息
	 */
	public function getUserInfoByEmail($email , $map) {
		if(empty($email)) {
			$this->error = L('PUBLIC_USER_EMPTY');			// 用户名不能为空
			return false;
		}
		$map['email'] = t($email);
		$data = $this->_getUserInfo($map);
		return $data;
	}

	/**
	 * 通过邮箱查询用户相关信息
	 * @param string $email 用户邮箱
	 * @return array 指定昵称用户的相关信息
	 */
	public function getUserInfoByDomain($email , $map) {
		if(empty($email)) {
			$this->error = L('PUBLIC_USER_EMPTY');			// 用户名不能为空
			return false;
		}
		$map['email'] = t($email);
		$data = $this->_getUserInfo($map);
		return $data;
	}

	/**
	 * 根据UID批量获取多个用户的相关信息
	 * @param array $uids 用户UID数组
	 * @return array 指定用户的相关信息
	 */
	public function getUserInfoByUids($uids) {
		!is_array($uids) && $uids = explode(',', $uids);

		foreach($uids as $v) {
			!$cacheList[$v] && $cacheList[$v] = $this->getUserInfo($v);
		}

		return $cacheList;
	}


	/**
	 * 返回最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
	
	/*
	 * 不知道干嘛用的？？？？？？？？？？？？？？？？
	 */
	public function getCircles($uid){
		/*return $this->where("uid=$uid")->relation(true)->find();*/

		$circles = array(array('cid'=>'0','cname'=>'安工大圈'));
		$records = M('CircleUser')->where("uid=$uid")->select();
		foreach($records as $record){
			$cid = $record['cid'];
			$cname = M('Circle')->where("cid=$cid")->getField('cname');
			$circles[] = array('cid'=>$cid,'cname'=>$cname);
		}
		return $circles;
	}
}
?>