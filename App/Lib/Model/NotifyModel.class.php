<?php
/**
 * 消息通知节点模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class NotifyModel extends Model {
	protected $_config = array();			// 配置字段
	protected $_table_prefix = null;		// 表前缀

/*	protected $site = array(
						'site_closed'              => '0',//0为不关闭站点,1为关闭站点
						'site_name'                => '安工大圈',
						'site_slogan'              => '为广大大学生提供优质服务的社交化网站',
						'site_closed_reason'       => '本站正在升级中...请敬请期待啊...',
						'sys_domain'               => 'admin',
						'sys_nickname'             => '管理员,超级管理员',
						'sys_email'                => 'xiaoluo@angongda.com',
					);*/
	
	/**
	 * 初始化方法，获取站点名称、系统邮箱、找回密码的URL
	 * @return void
	 */
	public function _initialize() {
		$site = D('Xdata')->get('admin_Config:site');
		$this->_config['site'] = $site['site_name'];
		$this->_config['site_url'] = SITE_URL;
		$this->_config['kfemail'] = 'mailto:'.$site['sys_email'];
		$this->_table_prefix = C('DB_PREFIX');	//表前缀为'q_'
		//$this->_config['findpass'] = U('public/Passport/findPassword');
	}
	
	/**
	 * 获取指定节点信息
	 * @param string $node 节点Key值
	 * @return array 指定节点信息
	 */
	public function getNode($node) {
		$list = $this->getNodeList();
		return $list[$node];
	}
	
	/**
	 * 获取指定节点的详细信息
	 * @param string $node 节点Key值
	 * @param array $config 配置数据
	 * @return array 指定节点的详细信息
	 */
	public function getDataByNode($node, $config) {
		empty($config) && $config = array();
		$config = array_merge($this->_config, $config);
		$nodeInfo = $this->getNode($node);
		$d['title'] = L($nodeInfo['title_key'], $config);
		$d['body'] = L($nodeInfo['content_key'], $config);
		return $d;
	}

	
	/**
	 * 获取节点列表
	 * @return array 节点列表数据
	 */
	public function getNodeList() {
		if(($list = D('Cache')->get('notify_node')) == false) {
			$list = M('Notify_node')->select();
			$list = $this->getHashList($list);
			D('Cache')->set('notify_node', $list);
		}
		return $list;
	}
	
	/**
	 * 将二维数组的第一维索引改成该索引所在数组中node索引对应的值
	 * @return array 节点列表数据
	 */
	public function getHashList($data, $fieldName = 'node', $field = '*'){
		$array = array();
		$list = array();
		foreach($data as $v){
			if($field != '*'){
				if(is_array($field)){
					foreach($field as $s){
						$array[$s] = $v[$s];
					}
				}else{
					$array[$field] = $v[$field];
				}
				$list[$v[$fieldName]] = $array;
			}
			$list[$v[$fieldName]] = $v;
		}
		return $list;
	}
	
	/**
	 * 保存节点配置
	 * @param array $data 节点修改信息
	 * @return boolean 是否保存成功
	 */
	public function saveNodeList($data) {
		foreach($data as $k => $v) {
			$m = $s = array();
			$m['node'] = $k;
			$s['send_email'] = intval($v['send_email']);
			$s['send_message'] = intval($v['send_message']);
			M('Notify_node')->where($m)->save($s);
		}

		$this->cleanCache();
		return true;
	}

	/**
	 * 清除消息节点缓存
	 * @return void
	 */
	public function cleanCache() {
		model('Cache')->rm('notify_node');
	}

	/**
	 * 分组返回指定用户的系统消息列表
	 * @param integer $uid 用户ID
	 * @return array 分组返回指定用户的系统消息列表
	 */
	public function getMessageList($uid) {
		$where['uid'] = $uid;
		$field = 'MAX(id) AS id, appname';
		if(!$list['group'] = M('Notify_message')->where($where)->field($field)->group('appname')->select()) {
			return array();
		}
		$where['is_read'] = 0;
		$field = 'COUNT(id) AS nums,appname';
		$list['groupCount'] = M('Notify_message')->where($where)->field($field)->group('appname')->select();
		foreach($list['group'] as $v) {
			$list['appInfo'][$v['appname']] = $v['appname'];
			$idHash[] = $v['id'];
		}
		$m['id'] = array('IN', $idHash);
		$list['messageInfo'] = M('Notify_message')->where($m)->select();
		$list['messageInfo'] = $this->getHashList($list['messageInfo'], 'id', '*');
		return $list;
 	}
	/*
	getMessageList操作方法返回的结果为
	array
	  'group' => 
			array
			  0 => 
				array
				  'id' => string '8' (length=1)
				  'appname' => string 'public' (length=6)
			  1 => 
				array
				  'id' => string '11' (length=2)
				  'appname' => string 'sdf' (length=3)
	  'groupCount' => 
			array
			  0 => 
				array
				  'nums' => string '2' (length=1)
				  'appname' => string 'public' (length=6)
			  1 => 
				array
				  'nums' => string '2' (length=1)
				  'appname' => string 'sdf' (length=3)
	  'appInfo' => 
			array
			  'public' => string 'public' (length=6)
			  'sdf' => string 'sdf' (length=3)
	  'messageInfo' => 
			array
			  8 => 
				array
				  'id' => string '8' (length=1)
				  'uid' => string '2' (length=1)
				  'node' => string 'sys_notify' (length=10)
				  'appname' => string 'public' (length=6)
				  'title' => string '' (length=0)
				  'body' => string '{$content}' (length=10)
				  'ctime' => string '0' (length=1)
				  'is_read' => string '0' (length=1)
			  11 => 
				array
				  'id' => string '11' (length=2)
				  'uid' => string '2' (length=1)
				  'node' => string 'qweqw' (length=5)
				  'appname' => string 'sdf' (length=3)
				  'title' => string 'sdfsdfasdfasdf' (length=14)
				  'body' => string 'sdfsdf' (length=6)
				  'ctime' => string '12312' (length=5)
				  'is_read' => string '0' (length=1)
	*/
	
 	/**
 	 * 获取指定应用指定用户下的系统消息列表
 	 * @param string $app 应用Key值
 	 * @param integer $uid 用户ID
 	 * @return array 指定应用指定用户下的系统消息列表
 	 */
	public function getMessageDetail($app, $uid) {		
		$where['appname'] = $app;
		$where['uid'] = $uid;
		$list = M('Notify_message')->where($where)->order('id DESC')->limit(20)->select();
		return $list;
	}
	/**
	getMessageDetail操作方法返回的结果为：
	array
	  0 => 
		array
		  'id' => string '9' (length=1)
		  'uid' => string '2' (length=1)
		  'node' => string 'sys_notify' (length=10)
		  'appname' => string 'public' (length=6)
		  'title' => string '' (length=0)
		  'body' => string '{$content}' (length=10)
		  'ctime' => string '0' (length=1)
		  'is_read' => string '0' (length=1)
	  1 => 
		array
		  'id' => string '8' (length=1)
		  'uid' => string '2' (length=1)
		  'node' => string 'sys_notify' (length=10)
		  'appname' => string 'public' (length=6)
		  'title' => string '' (length=0)
		  'body' => string '{$content}' (length=10)
		  'ctime' => string '0' (length=1)
		  'is_read' => string '0' (length=1)
	*/

	/**
	 * 更改指定用户的消息从未读为已读
	 * @param integer $uid 用户ID
	 * @param string $appname 应用Key值
	 * @return mix 更改失败返回false，更改成功返回消息ID
	 */
	public function setRead($uid, $appname = '') {
		$where['uid'] = $uid;
		!empty($appname) && $where['appname'] = $appname;
		$s['is_read'] = 1;
		return M('Notify_message')->where($where)->save($s);
	}

	/**
	 * 获取指定用户未读消息的总数
	 * @param integer $uid 用户ID
	 * @return integer 指定用户未读消息的总数
	 */
	public function getUnreadCount($uid){
		$where['uid'] = $uid;
		$where['is_read'] = 0;
		return M('Notify_message')->where($where)->count();
	}
	
	/**
	 * 发送消息入口，对已注册用户发送的消息都可以通过此函数
	 * @param array $toUid 接收消息的用户ID数组
	 * @param string $node 节点Key值
	 * @param array $config 配置数据-->不知道需要什么，也不知道怎么用
	 * @param intval $from 消息来源用户的UID
	 * @return void
	 */
	public function sendNotify($toUid, $node, $config, $from) {
		empty($config) && $config = array();
		$config = array_merge($this->_config, $config);

		$nodeInfo = $this->getNode($node);
		if(!$nodeInfo) {
			return false;
		}
		
		!is_array($toUid) && $toUid = explode(',', $toUid);
		$userInfo = D('User')->getUserInfoByUids($toUid);

		$data['node'] = $node;
		$data['appname'] = $nodeInfo['appname'];
		$data['title'] = L($nodeInfo['title_key'], $config);
		$data['body'] = L($nodeInfo['content_key'], $config);
		foreach($userInfo as $v) {
			$data['uid'] = $v['uid'];
			$data['name'] = $v['nickname'];
			$nodeInfo['send_message'] == 1 && $this->sendMessage($data);
			$data['email'] = $v['email'];
			if( $nodeInfo['send_email'] == 1 ){
				if(in_array($node,array('atme','comment','new_message'))){
					/**
					 +--------------------------------------------------------------------------------------
					 * TODO:用户隐私设置
					 +--------------------------------------------------------------------------------------
					 **/
					echo "<script>alert('该项隐私设置没有设置好');</script>";
					//$map['key'] = $node.'_email';
					//$map['uid'] = $v['uid'];
					
					//$isEmail = D('user_privacy')->where($map)->getField('value');
					//$isEmail == 1 && $this->sendEmail($data);
				}else{
					$this->sendEmail($data);
				}
			}
		}
	}
	

	/**
	 * 发送邮件，添加到消息队列数据表中
	 * @param array $data 消息的相关数据=>需要用户ID,节点,邮箱,名字,应用名称,标题,内容
	 * @return mix 添加失败返回false，添加成功返回新数据的ID
	 */
	public function sendEmail($data) {
		if(empty($data['email'])) {
			// TODO:邮箱格式验证
			return false;
		} 
		// TODO:----------XXXXXXXXX(弃用-->用户隐私设置判断)
		$s['uid'] = intval($data['uid']);
		$s['node'] = $data['node'];
		$s['email'] = $data['email'];
		$s['name'] = $data['name'];
		$s['appname'] = $data['appname'];
		$s['is_send'] = 1;
		$s['sendtime'] = time();
		$s['title'] = $data['title'];
		$body = html_entity_decode($data['body']);
		$s['body']= '<div style="width:540px;border:#0F8CA8 solid 2px;margin:0 auto"><div style="color:#bbb;background:#0f8ca8;padding:5px;overflow:hidden;zoom:1"><div style="float:right;height:15px;line-height:15px;padding:10px 0;display:none">2012年07月15日</div>
					<div style="float:left;overflow:hidden;position:relative"><a><img style="border:0 none" src="__PUBLIC__/Images/LOGO/yunfou.png"></a></div></div>
					<div style="background:#fff;padding:20px;min-height:300px;position:relative">		<div style="font-size:14px;">			
						            	<p style="padding:0 0 20px;margin:0;font-size:12px">'.$body.'</p>
						            </div></div><div style="background:#fff;">
			            <div style="text-align:center;height:18px;line-height:18px;color:#999;padding:6px 0;font-size:12px">此信为系统自动邮件,请不要直接回复。</a></div>
			            <div style="line-height:18px;text-align:center"><p style="color:#999;font-size:12px">&copy;2013 yunfou All Rights Reserved.</p></div>
			        </div></div>';
		$s['ctime'] = time();
		if(D('Mail')->send_email($s['email'],$s['name'],$s['title'],$s['body'])){
			return M('Notify_email')->add($s);
		}else{
			$s['is_send'] = 0;
			M('Notify_email')->add($s);
			$this->_error = L('PUBLIC_EMAIL_TO_ERROR'); // 发送邮箱失败
			return false;
		}
	}
	/**
	 * 发送系统消息，给指定用户
	 * @param array $data 发送系统消息相关数据
	 * @return mix 发送失败返回false，发送成功返回新的消息ID
	 */
	public function sendMessage($data) {
		if(empty($data['uid'])) {
			return false;
		}
		$s['uid'] = intval($data['uid']);
		$s['node'] = t($data['node']);
		$s['appname'] = t($data['appname']);
		$s['is_read'] = 0;
		$s['title'] = t($data['title']);
		$s['body'] = $data['body'];
		$s['ctime'] = time();
		return M('Notify_message')->add($s);
	}

	/**
	 * 删除通知
	 * @param integer $id 通知ID
	 * @return mix 删除失败返回false，删除成功返回删除的通知ID
	 */
	public function deleteNotify($id) {
		$where['uid'] = $_SESSION[C('USER_AUTH_KEY')];		// 仅仅只能删除登录用户自己的通知
		$where['id'] = intval($id);
		return M('Notify_message')->where($where)->delete();	
	}

	/**
	 * 发送邮件队列中的数据，每次执行默认发送10封邮件
	 * @param integer $sendNums 发送邮件的个数，默认为10
	 * @return array 返回取出的数据个数与实际发送邮件的数据个数
	 */
	public function sendEmailList($sendNums = 10) {
		set_time_limit(0);
		$mail = D('Mail');
		$find['is_send'] = 0;
		$list = M('Notify_email')->where($find)->limit($sendNums)->order('id ASC')->select();
		$r['count'] = count($list);
		$r['nums'] = 0;
		ob_start();
		foreach($list as $v) {
			$where['id'] = $v['id'];
			$save['is_send'] = 1;
			$save['sendtime'] = time();
			if($mail->send_email($v['email'], $v['title'], $v['body'])){
				M('Notify_email')->where($where)->save($save);
				$r['nums']++;
			}
			// TODO:现在默认为全部发送成功，如果后期发送失败要修改回来的话再根据$v['id']修改is_send
		}
		ob_end_clean();
		return $r;
	}

	/**
	 * 发送系统消息，给用户组或全站用户
	 * @param array $user_group 用户组ID
	 * @param string $content 发送信息内容
	 * @return boolean 是否发送成功
	 */
	public function sendSysMessae($user_group, $content) {
    	set_time_limit(0);
    	$ctime = time();
    	$user_group = intval($user_group);
    	if(!empty($user_group)) { 
    		// 判断组中是否存在用户
    		$m['user_group_id'] = $user_group;
    		$c = D('UserGroupLink')->where($m)->count();
    		if($c > 0) {
    			// 针对用户组
    			$sql = "INSERT INTO ".$this->tablePrefix."notify_message (`uid`,`node`,`appname`,`title`,`body`,`ctime`,`is_read`)
    				SELECT uid,'sys_notify','public','','{$content}','{$ctime}','0' 
    				FROM ".$this->tablePrefix."user_group_link WHERE user_group_id = {$user_group} ";
    		} else {
    			return true;
    		}		
    	} else {
    		// 全站用户
    		$sql = "INSERT INTO ".$this->tablePrefix."notify_message (`uid`,`node`,`appname`,`title`,`body`,`ctime`,`is_read`)
    				SELECT uid,'sys_notify','public','','{$content}','{$ctime}','0' 
    				FROM ".$this->tablePrefix."user WHERE is_del=0 ";
    	}

    	D('')->query($sql);
    	return true;
	}

	/*** API使用 ***/

		/**
	 * 系统对用户发送通知
	 * @param string|int|array $receive 接收人ID 多个时以英文的","分割或传入数组
	 * @param string           $type    通知类型, 必须与模版的类型相同, 使用下划线分割应用.
	 * 					   				如$type = "weibo_follow"定位至/apps/weibo/Language/cn/notify.php的"weibo_follow"
	 * @param array            $data
	 * @return void
	 */
	public function sendIn( $receive , $type , $data  ) {
		return $this->__put( $receive , $type , $data  , 0 , true );
	}
	
		/**
	 +----------------------------------------------------------
	 * Description 通知发送处理
	 +----------------------------------------------------------
	 * @author Nonant nonant@thinksns.com
	 +----------------------------------------------------------
	 * @param $type    通知类型
	 * @param $receive 通知接收者的用户ID,类型可为 数字、字符串、数组
	 * @param $title   通知标题
	 * @param $body    通知内容
	 * @param $from    通知发送者UID
	 * @param $system  是否为系统通知
	 +----------------------------------------------------------
	 * @return Boolen
	 +----------------------------------------------------------
	 * Create at  2010-9-13 下午04:24:53
	 +----------------------------------------------------------
	 */
	private function __put($receive,$type,$data,$from=0,$system=false) {
		global $ts;
		$receive = $this->_parseUser( $receive ); if(!$receive) return false;
		$from = ( $system==false &&  $from==0 ) ? $ts['user']['uid'] : $from ;
		$data      = addslashes(serialize( $data ));
		$time       = time();

		//优化大批量发送通知，讲数据切割处理，每次插入100条
		$receive	=	array_chunk($receive, 100)  ;
		foreach ($receive as $receive_chunck){

			foreach ($receive_chunck as $k=>$v){
				if($v==$from) continue;
				$sqlArr[] = "($from,$v,'$type','$data',$time)";
			}

			if( $sqlArr ){
				$sql = "INSERT INTO ".C('DB_PREFIX')."notify (`from`,`receive`,`type`,`data`,`ctime`) values ".implode(',',$sqlArr);
				$result[] = M('Notify')->execute($sql);

			}

			unset($sql,$sqlArr,$receive_chunck);
		}

		return $result;
	}

	//解析传入的用户ID
	private function _parseUser($touid){
		if( is_numeric($touid) ){
			$sendto[] = $touid;
		}elseif ( is_array($touid) ){
			$sendto = $touid;
		}elseif (strpos($touid,',') !== false){
			$touid = array_unique(explode(',',$touid));
			foreach ($touid as $key=>$value){
				$sendto[] = $value;
			}
		}else{
			$sendto = false;
		}
		return $sendto;
	}
}
?>