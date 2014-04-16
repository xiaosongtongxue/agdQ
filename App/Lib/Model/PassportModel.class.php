<?php
/**
 * 通行证模型 - 业务逻辑模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class PassportModel{

	protected $_error = null;				// 错误信息字段
	protected $rel = array();				// 判断是否是第一次登录
	//得到需要验证的节点数组
	private function getNodeList(){
		$node = array();
		$apps = M('Node')->field('id,name')->where(array('level'=>1))->select();
		foreach($apps as $key=>$app){
			$appId	=	$app['id'];
			$appName	 =	 $app['name'];
			$node[strtoupper($appName)]   =  array();
			$modules = M('Node')->field('id,name')->where(array('level'=>2,'pid'=>$appId))->select();
			foreach ($modules as $module){

				$moduleId = $module['id'];
				$moduleName = $module['name'];
				$node[strtoupper($appName)][strtoupper($moduleName)]   =  array();
				$actions = M('Node')->field('id,name')->where(array('level'=>3,'pid'=>$module['id']))->select();
				foreach ($actions as $action){
					$actionId = $action['id'];
					$actionName = $action['name'];

					$a[strtoupper($actionName)] = $actionId;
				}
				$node[strtoupper($appName)][strtoupper($moduleName)] =  $a;
			}
		}
		return $node;
	}

	/**
	 * 使用本地帐号登陆（密码为null时不参与验证）
	 * @param string $login 登录名称，邮箱或用户名
	 * @param string $password 密码
	 * @param boolean $is_remember_me 是否记录登录状态，默认为false
	 * @return boolean 是否登录成功
	 */
	public function loginLocal($login, $password = null, $is_remember_me = false) {
		$user = $this->getLocalUser($login, $password);
		return $user['uid']>0 ? $this->_recordLogin($user['uid'], $is_remember_me) : false;
	}

	/**
	 * 根据标示符（email或stu_id）和未加密的密码获取本地用户（密码为null时不参与验证）
	 * @param string $login 标示符内容（为数字时：标示符类型为uid，其他：标示符类型为email）
	 * @param string|boolean $password 未加密的密码
	 * @return array|boolean 成功获取用户数据时返回用户信息数组，否则返回false
	 * icubit 于2013-9-17修改
	 */
	public function getLocalUser($login, $password, $type='person') {
		$login = t($login);
		if(empty($login) || empty($password)) {
			$this->_error = L('PUBLIC_ACCOUNT_EMPTY');			// 帐号或密码不能为空
			return false;
		}

		if(is_numeric($login)) {
			$map['param'] = $login;
		} else {
			$map['email'] = $login;
		}
		$map['is_del'] = 0;
		$map['type'] = $type;
		if(!$user = M('User')->where($map)->find()) {
			$this->_error = L('PUBLIC_ACCOUNT_NOEXIST');			// 帐号不存在
			return false;
		}

		if($user['is_active'] == 0){
			$this->_error = L('PUBLIC_ACCOUNT_NOT_ACTIVATED');			// 该账号没有激活,请到邮箱激活,谢谢
			return false;
		}

		$uid  = $user['uid'];
		// 记录登陆日志，首次登陆判断
		$this->rel = M('Login_record')->where("uid = ".$uid)->field('locktime')->find();

		if($this->rel['locktime'] > time()) {
			$this->_error = L('PUBLIC_ACCOUNT_LOCKED');			// 您的帐号已经被锁定，请稍后再登录
			return false;
		}

		if( md5($password) != $user['pwd'] ) {
			// 记录账号
			$save['ip'] = get_client_ip();
			$save['ctime'] = time();
			$m['uid'] = $save['uid'] = $uid;

			if(empty($this->rel)) {
				M('Login_record')->add($save);
			} else {
				M('Login_record')->where($m)->save($save);
			}
			$this->_error = L('PUBLIC_ACCOUNT_PASSWORD_ERROR');			// 您输入的密码错误
			return false;
		} else {
			$logData['uid'] = $uid;
			$logData['ip'] = get_client_ip();
			$logData['ctime'] = time();
			M('Login_logs')->add($logData);
			return $user;
		}
	}

	/**
	 * 设置登录状态、记录登录日志
	 * @param integer $uid 用户ID
	 * @param boolean $is_remember_me 是否记录登录状态，默认为false
	 * @return boolean 操作是否成功
	 */
	private function _recordLogin($uid, $is_remember_me = false) {

		// 注册cookie
		if(!$this->getCookieUid() && $is_remember_me ) {
			$expire = 3600 * 24 * 365;  //
			cookie('AGDQ_LOGGED_USER',jiami(C('SECURE_CODE').$uid), $expire);
		}

		// 记住活跃时间------------半个小时
		cookie('AGDQ_ACTIVE_TIME',$_SERVER['REQUEST_TIME'] + 3600 * 24 * 14);

		// 更新登陆时间和登录IP
		$field = array('last_login_time' => $_SERVER['REQUEST_TIME'] , 'login_ip' => get_client_ip());
		M('User')->where('`uid`="'.$uid.'"')->setField($field);

		// 记录登陆日志，首次登陆判断
		empty($this->rel) && $this->rel	= M('Login_record')->where("uid = ".$uid)->getField('login_record_id');

		$rbac            =   array();

        // 支持使用绑定帐号登录
        /* $rbac['uid']	= intval($uid);
        $rbac["status"]	=	array('gt',0); */

        //import('ORG.Util.RBAC' );
        //$authInfo = RBAC::authenticate($rbac);

        //使用用户名、密码和状态的方式进行认证
        if(false/*  === $authInfo */) {
            $this->error('帐号不存在或已禁用！');
        }else {
            //$_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['uid'];
        	 $_SESSION[C('USER_AUTH_KEY')]	=	$uid;
			/*$_SESSION['nickname'] = $authInfo['nickname']; */
			$_SESSION['userInfo'] = D('User')->getUserInfo($uid);

			if(in_array($_SESSION['userInfo']['email'],C('SUPER_ADMIN'))) {
                $_SESSION[C('ADMIN_AUTH_KEY')]		=	true;
            }

			$map['ip'] = get_client_ip();
			$map['ctime'] = time();
			$map['locktime'] = 0;

			if($this->rel) {
				M('Login_record')->where("uid = ".$uid)->save($map);
			} else {
				$map['uid'] = $uid;
				M('Login_record')->add($map);
			}

            // 缓存访问权限
           // RBAC::saveAccessList();
            // 缓存需认证的节点
           // $_SESSION['_NODE_LIST'] = $this->getNodeList();

			return true;
        }
	}

	/**
	 * 获取cookie中记录的用户ID
	 * @return integer cookie中记录的用户ID
	 */
	public function getCookieUid() {
		static $cookie_uid = null;
		if(isset($cookie_uid) && $cookie_uid !== null) {
			return $cookie_uid;
		}

		$cookie = cookie('AGDQ_LOGGED_USER');

		$cookie = explode(".", jiemi($cookie));

		$cookie_uid = ($cookie[0] != C('SECURE_CODE')) ? false : $cookie[1];

		return $cookie_uid;
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