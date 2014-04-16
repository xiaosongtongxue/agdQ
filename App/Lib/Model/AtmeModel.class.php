<?php
/**
 * @me模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class AtmeModel extends Model {
	protected $tableNmae = 'atme';
	protected $fields = array(  0 => 'atme_id',    //主键，@我的编号
								1 => 'app',        //所属应用
								2 => 'table',      //存储应用内容的表名
								3 => 'row_id',     //应用含有@的内容的编号
								4 => 'uid',        //被@的用户的编号
								'_pk' => 'atme_id');

	protected $_app = null;                       // 所属应用
	protected $_app_table = null;                 // 所属资源表
	protected $_app_pk_field = null;              // 应用主键字段
	protected $_at_regex = "/@(.+?)([\s|:]|$)/is";//"/@{uid=([^}]*)}/";    // @正则规则
	protected $_at_field = 'uid';                 // @的资源字段
	protected $uid;                               // 登录用户的UID
	protected $_user_data_model;                  // 用户统计数据模型
	
	/**
	 * 初始化方法，设置默认用户信息，初始化用户统计数据模型
	 * @return void
	 */
	public function _initialize() {
		$this->uid = $_SESSION[C('USER_AUTH_KEY')];
		$this->_app = 'Public';
		$this->_user_data_model = D('UserData');
	}

	/**
	 * 设置所属应用
	 * @param string $app 应用名称
	 * @return object @对象
	 */
	public function setAppName($app) {
		$this->_app = $app;
		return $this;
	}

	/**
	 * 设置相关内容所存储的资源表
	 * @param string $app_table 数据表名
	 * @return object @对象
	 */
	public function setAppTable($app_table) {
		$this->_app_table = $app_table;
		return $this;
	}

	/**
	 * 设置@的相关正则规则
	 * @param string $regex 正则规则
	 * @return object @对象
	 */
	public function setAtRegex($regex) {
		$this->_at_regex = $regex;
		return $this;
	}

	/**
	 * 设置@的资源字段
	 * @param string $field @的资源字段
	 * @return object @对象
	 */
	public function setAtField($field) {
		$this->_at_field = $field;
		return $this;
	}

	/**
	 * 获取@Me列表
	 * @param array $where 查询条件
	 * @param string $order 排序条件，默认为atme_id DESC
	 * @param integer $limit 结果集显示个数，默认为20
	 * @return array @Me列表信息
	 */
	public function getAtmeList($where = null, $order = 'atme_id DESC', $limit = 20) {
		!$where['app'] && $this->_app && ($where['app'] = $this->_app);
		!$where['table'] && $this->_app_table && ($where['table'] = $this->_app_table);

		// $data = $this->where($map)->order($order)->findPage($limit);
        // 格式化数据
        /*foreach($data['data'] as &$v) {
        	$v = model('Source')->getSourceInfo($v['table'], $v['row_id'], false, $v['app']);
        }*/
		
		$returns = array();
		$return = array();
		//获取@Me列表的信息
		$data = $this->where($where)->order($order)->limit($limit)->select();
		foreach($data as $v){
			$return['atme_type'] = $v['table'];
			switch($v['table']){
				case 'feed':		
					$temp = D('Feed')->getFeedByIds(array($v['row_id']));
					$return = array_merge($return,$temp[0]);
					//$return['data'] = $temp[0];
					break;
				case 'comment':
					$temp = D('Comment')->getCommentById($v['row_id'],true);
					$return = array_merge($return,$temp);
					break;
			}
			$returns[] = $return;
		}
		/*
		+--------------------------------------------------------------
		    TODO: 格式化数据
		+--------------------------------------------------------------
		 */

		//重置@me的未读数
		$where['uid'] && D('UserCount')->resetUserCount($where['uid'], 'unread_atme', 0);

		return $returns;
	}

	/**
	 * 添加@Me数据
	 * @param string $content @Me的相关内容
	 * @param integer $row_id 资源ID
	 * @param array $extra_uids 额外@用户ID
	 * @param array $less_uids 去除@用户ID
	 * @return integer 添加成功后的@ID
	 */
	 public function addAtme($content, $row_id, $extra_uids = null, $less_uids = null) {
		//去除重复、空值与自己
		$extra_uids = array_diff($extra_uids, array($this->uid));
		$extra_uids = array_unique($extra_uids);
		$extra_uids = array_filter($extra_uids);

		$less_uids[] = $this->uid;
		$less_uids = array_unique($less_uids);
		$less_uids = array_filter($less_uids);
		//获取@用户的UID数组
		$uids = $this->getUids($content, $extra_uids, $row_id, $less_uids);
		//添加@信息
		$return = $this->_saveAtme($uids, $row_id);

		return $return;
	}

	/*添加@Me数据
	 * @param string $table @me内容所属表名
	 * @param integer $row_id @me内容的编号
	 * @param integer $row_id @me用户ID
	 */
	/*public function addAtme($table,$row_id,$uid){
		if(!$this->add(array('table'=>$table,'row_id'=>$row_id,'uid'=>$uid,'is_read'=>0)))
			return false;
		//更新未读评论数
		D('UserData')->updateKey('unread comment',1,true,$uid);
		return true;
	}*/
	/**
	 * 获取@内容中的@用户
	 * @param string $content @Me的相关内容
	 * @param array $extra_uids 额外@用户UID
	 * @param integer $row_id 资源ID
	 * @param array $less_uids 去除@用户ID
	 * @return array 用户UID数组
	 */
	public function getUids($content, $extra_uids = null, $row_id, $less_uids = null) {
		/*
		array
		  0 =>
		    array
		      0 => string '@are ' (length=5)
		  1 =>
		    array
		      0 => string 'are' (length=3)
		  2 =>
		    array
		      0 => string ' ' (length=1)
		 */
		//正则匹配内容
		preg_match_all($this->_at_regex, $content, $matches);
		$nickname = $matches[1];
		$where = "nickname in ('" . implode("','", $nickname) . "')";
		$uidList = M('User')->where($where)->field('uid')->select();
		//将二维数组转为一维数组
		$matchuids = getSubByKey($uidList, 'uid');
		//如果内容匹配中没有用户
		if (empty($matchuids) && !empty($extra_uids)) {
			//去除@用户ID
			if (!empty($less_uids)) {
				foreach ($less_uids as $k => $v) {
					if (in_array($v, $extra_uids))
						unset($extra_uids[$k]);
				}
			}
			return is_array($extra_uids) ? $extra_uids : array($extra_uids);
		}
		//如果匹配内容中存在用户
		$suid = array();
		foreach ($matchuids as $v) {
			!in_array($v, $suid) && $suid[] = (int) $v;
		}
		//去除@用户ID
		if (!empty($less_uids)) {
			foreach ($suid as $k => $v) {
				if (in_array($v, $less_uids)) {
					unset($suid[$k]);
				}
			}
		}

		/*
		+--------------------------------------------------------------
		    TODO: 发邮件流程
		+--------------------------------------------------------------
		 */
		return array_unique(array_filter(array_merge($suid, (array) $extra_uids)));
	}

	/**
	 * 添加@Me信息操作
	 * @param array $uids 用户UID数组
	 * @param integer $row_id 资源ID
	 * @return integer 添加成功后的@ID
	 */
	private function _saveAtme($uids, $row_id) {
		foreach ($uids as $u_v) {
			//去除自己@自己的数据
			if ($u_v == $this->uid) {
				continue;
			}
			$data[] = "('{$this->_app}', '{$this->_app_table}', {$row_id}, {$u_v})";
			//更新@Me的未读数目
			D('UserCount')->updateUserCount($u_v, 'unread_atme', 1);
		}
		!empty($data) && $res = $this->query('INSERT INTO ' . C('DB_PREFIX') . 'atme (`app`, `table`, `row_id`, `uid`) VALUES ' . implode(',', $data));

		return $res;
	}
	
	/**
	 * 更新最近@的人
	 * @param string $content 原创微博内容
	 */
	public function updateRecentAt( $content ){
		$uid = $_SESSION[C('USER_AUTH_KEY')];
		// 获取@用户的UID数组
		preg_match_all($this->_at_regex, $content, $matches);
		$nickname = $matches[1];
		if ( $nickname[0] ){
		//	$map = "nickname in ('".implode("','",$nickname)."') AND uid!=".$uid.' AND `is_audit`=1 AND `is_active`=1';
			$map = "nickname in ('".implode("','",$nickname)."') AND uid!=".$uid.' AND `is_active`=1';
			//$Model->query('SELECT * FROM think_user WHERE status = 1');
			$ulist = D('User')->where($map)->field('uid')->select();
			$matchuids = getSubByKey($ulist,'uid');
			$userdata = D( 'UserData' );
			$value = $userdata->where('uid='.$uid." and `key`='user_recentat'")->getField('value');
			if ( $value ){
				$atdata = getSubByKey( unserialize( $value ) , 'uid');
				$atdata && $matchuids = array_merge( $matchuids , $atdata);
				$matchuids = array_unique( $matchuids );
				$matchuids = array_slice( $matchuids , 0 , 10 );
				$users = D( 'User' )->getUserInfoByUids( $matchuids );
				foreach ( $users as $v){
					if ( !$v['uid'] ){
						continue;
					}
					$udata[] = array('uid'=>$v['uid'],'uname'=>$v['uname'],'avatar_small'=>$v['avatar_small'],'search_key'=>$v['search_key']);
				}
				//更新userdata表里面的最近@的人的信息
				$userdata->setField('value' , serialize( $udata ) , "`key`='user_recentat' AND uid=".$uid);
			} else {
				$matchuids = array_slice( $matchuids , 0 , 10 );
				$users = D( 'User' )->getUserInfoByUids( $matchuids );
				foreach ( $users as $v){
					if ( !$v['uid'] ){
						continue;
					}
					$udata[] = array('uid'=>$v['uid'],'nickname'=>$v['nickname'],'avatar_small'=>$v['avatar_small'],'search_key'=>$v['search_key']);
				}
				$data['uid'] = $uid;
				$data['key'] = 'user_recentat';
				$data['value'] = serialize( $udata );
				$data['mtime'] = time();
				$userdata->add($data);
			}
		}
	}
	
	/**
     * 删除@Me数据
     * @param string $content @Me的相关内容
     * @param integer $row_id 资源ID
     * @param array $extra_uids 额外@用户UID
     * @return boolean 是否删除成功
     */
	public function deleteAtme($content, $row_id, $extra_uids = null){
		$uids = $this->getUids($content, $extra_uids);
		$result = $this->_deleteAtme($uids, $row_id);
		return $result;
	}

    /**
     * 删除@Me信息操作
     * @param array $uids 用户UID数组
     * @param integer $row_id 资源ID
     * @return boolean 是否删除成功
     */
	private function _deleteAtme($uids, $row_id){
		if(!empty($uids)){
			$where['table'] = $this->_app_table;
			$where['row_id'] = $row_id;
			$where['uid'] = array('IN', $uids);
			$res = $this->where($where)->delete();
			return $res;
		}else{
			$where['table'] = $this->_app_table;
			$where['row_id'] = $row_id;
			$res = $this->where($where)->delete();
			return $res;
		}
	}
}
?>