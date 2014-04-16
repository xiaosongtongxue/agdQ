<?php
/**
 * 信息模型 - 数据对象模型
 * @author icubit <icubit@qq.com>
 * @version agdQ1.0
 */
class FeedModel extends RelationModel {
	protected $_link = array(
		'FeedData'=>array(
			'mapping_type'   	=>HAS_ONE,
			'class_name'    	=>'FeedData',
			'mapping_name'		=>'FeedData',
			'foreign_key'		=>'feed_id',
			'mapping_fields '	=>'feed_content',
			'as_fields'			=>'feed_content',
			//'condition'			=>'',

			 // 定义更多的关联属性
		 ),
		 'User'=>array(
		 	'mapping_type'   	=>BELONGS_TO,
		 	'class_name'    	=>'User',
			'mapping_name'		=>'User',
			'foreign_key'		=>'uid',
			'mapping_fields'	=>'nickname',
			'as_fields'			=>'nickname',
		 ),
		 'Circle'=>array(
		 	'mapping_type'   	=>BELONGS_TO,
		 	'class_name'    	=>'Circle',
			'mapping_name'		=>'Circle',
			'foreign_key'		=>'cid',
			'mapping_fields'	=>'cname',
			'as_fields'			=>'cname',
		 ),
	 );

	protected $_auto = array (
		//array('status','1'),  // 新增的时候把status字段设置为1
		//array('password','md5',1,'function') , // 对password字段在新增的时候使md5函数处理
		//array('name','getName',1,'callback'), // 对name字段在新增的时候回调getName方法
		array('publish_time','time',3,'function'), // 对create_time字段在更新的时候写入当前时间戳
	);

	protected $tableName = 'feed';
	private $_error;		// 错误信息字段
	protected $type =  array(	//发表的类型
					'post',		//发表
					'repost',	//转载
					'postvideo',	//发表视频
					'postfile',		//发表文件
					'postimage',	//发表图片
					);
	/* protected $fields = array(
					'feed_id',                    //动态ID
					'uid',                        //产生动态的用户UID
					'cid',
					'type',                       //feed类型.由发表feed的程序控制
					'feed_type',                   //标签类型 默认为public
					'app',                        //feed来源的appname 默认为public
					'app_row_id',                 //关联资源所在的表
					'app_row_table',              //关联的来源ID（如博客的id）默认为0
					'publish_time',               //产生时间戳
					'is_del',                     //是否删除 默认为0
					'from',                       //客户端类型，0：网站；1：手机网页版；2：android；3：iphone 默认为0
					'comment_count',              //评论数 默认为0
					'repost_count',               //分享数 默认为0
					'comment_all_count',          //全部评论数目 默认为0
					'is_repost',                  //是否转发 0-否 1-是 默认为0
					'is_audit',                   //是否已审核 0-未审核 1-已审核 默认为1
					'_pk'=>'feed_id'
					); */
	public function test($map){
		return $this->where($map)->select();
	}
	/**
	 * 添加信息
	 * @param integer $uid 操作用户ID
	 * @param array $data 信息相关数据
	 * @param string $app 应用类型，默认为public
	 * @param string $type 类型，默认为post
	 * @param string $tag_type 标签类型，默认为public
 	 * @param boolean $is_repost 是否为转载，默认为0
	 * @param integer $app_id 应用资源ID，默认为0
	 * @param string $app_table 应用资源表名，默认为feed
	 * @param boolean $isSendMe 是否为进行发送，默认为true
	 * @return mix 添加失败返回false，成功返回新的微博ID
	 */
	public function put($data = array(),$content,$extUid = null, $lessUids = null, $isAtMe = true) {
		// 判断数据的正确性
		$app        = isset($data['app_name']) ? t($data['app_name']) : 'public';
		$uid        = isset($data['uid']) ? intval($data['uid']) : $_SESSION[C('USER_AUTH_KEY')];
		$cid        = isset($data['cid']) ? intval($data['cid']) : 0;
		$app_table  = isset($data['app_table']) ? strtolower($data['app_table']) : 'feed';
		$app_row_id = isset($data['app_row_id']) ? t($data['app_row_id']) : 0;
		$feed_type  = isset($data['feed_type']) ? intval($data['feed_type']) : 1;
		$type       = isset($data['type']) ? t($data['type']) : 'post';
		$type       = in_array( $type , $this->type ) ? $type : 'post';
		$from       = isset($data['from']) ? intval($data['from']) : 0;
		$is_repost  = isset($data['is_repost']) ? intval($data['is_repost']) : 0;
		// 添加feed表记录
		$data['uid']           = $uid;
		$data['cid']           = $cid;
		$data['feed_type']     = $feed_type;
		$data['app']           = $app;
		$data['type']          = $type;
		$data['app_row_id']    = $app_row_id;
		$data['app_row_table'] = $app_table;
		$data['publish_time']  = time();
		$data['from']          = $from;
		$data['is_del']        = $data['comment_count'] = $data['repost_count'] = 0;
		$data['is_repost']     = $is_repost;

		//TODO:判断是否先审后发
		$data['is_audit'] = 1;

		/**
		 -------------------------------------------
		 // TODO:微博内容处理
		 -------------------------------------------
		 */

		/**
		 -------------------------------------------
		 // TODO:分享到微博的应用资源，加入原资源链接
		 -------------------------------------------
		 // $data['body'] .= $data['source_url'];
		 // $data['content'] .= $data['source_url'];
		 */

		//unset($data['content']);
		// 添加微博信息
		$feed_id =  $this->data($data)->add();
		if(!$feed_id)	return false;

		/**
		 -------------------------------------------
		 // TODO:该信息为通过审核时给用户发表通知信息提醒
		 -------------------------------------------
		 */

		/**
		 -------------------------------------------
		 // TODO:添加关联数据,将feed和feeddata表关联起来
		 -------------------------------------------
		 */
		$feed_data = D('FeedData')->data(array('feed_id'=>$feed_id,'feed_data'=>serialize($data),'client_ip'=>get_client_ip(),'feed_content'=>$content))->add();


		// 添加微博成功后
		if($feed_data) {
			//微博发布成功后的钩子
			//Addons::hook("weibo_publish_after",array('weibo_id'=>$feed_id,'post'=>$data));
			// 统计数修改
			D('UserData')->setUid($uid)->updateKey('feed_count', 1);
			// 发送通知消息 - 重点
			if($data['type'] == 'repost') {
				// 转发微博
				//$isAtMe && $content = $data['content'];			// 内容用户
				//原feed转发数加一
				$this->where(array('feed_id'=>$app_row_id))->setInc('repost_count');
				// 资源作者用户
				$source_uid = M('Feed')->where(array('feed_id'=>$data['app_row_id']))->getField('uid');
				$extUid[] = $source_uid;			
				/* if($isAtMe && !empty($data['curid'])) {
					// 上节点用户
					$appRowData = $this->get($data['curid']);
					$extUid[] = $appRowData['uid'];
				} */	
			} else {
				// 其他微博
				//$content = $data['content'];
				//更新最近@的人
				D( 'Atme' )->updateRecentAt( $content );								// 内容用户
			}
			// 发送@消息
			D('Atme')->setAppName('Public')->setAppTable('feed')->addAtme($content, $feed_id, $extUid, $lessUids);

			/*$data['client_ip'] = get_client_ip();
			$data['feed_id'] = $feed_id;
			$data['feed_data'] = serialize($data);
			// 主动创建渲染后的缓存
			//$return = $this->setFeedCache($data);

			$return['user_info'] = D('User')->getUserInfo($uid,'uid,nickname');
			//$return['GroupData'] = D('UserGroupLink')->getUserGroupData($uid);   //获取用户组信息
			$return['feed_id'] = $feed_id;
			$return['app_row_id'] = $data['app_row_id'];
			$return['content'] = $content;
			$return['feed_type'] = $data['feed_type'];
			$return['publish_time'] = $data['publish_time'];*/
			$temp = $this->getFeedByIds(array($feed_id));
			$return = $temp[0];//为何是数组下标为零赋值呢？！？？？？？？？？？？？？？？？？？？？？？？？？？？？？
			if(!$return) {
				$this->error = L('PUBLIC_CACHE_FAIL');				// Feed缓存写入失败
			}
			return $return;
		} else {
			$this->error = L('PUBLIC_ADMIN_OPRETING_ERROR');		// 操作失败
			return false;
		}
	}

	public function getFeedTypes(){
		return M('FeedType')->select();
	}
	/**
	 * 格式化一条feed,用于页面输出
	 * @param array  $record  一条feed记录
	 * @return mix 返回格式化后的feed记录,可以用于页面输出
	 * @auther icubit
	 */
	private function formatFeedByRecord($record){
		if(empty($record))
			return false;
		$temp = array('feed_id'		=>$record['feed_id'],
						  'uid'		=>$record['uid'],
						  'cid'		=>$record['cid'],
					'feed_type'		=>$record['feed_type'],
							'type'	=>$record['type'],
					'source_feed_del'=>false,
				'app_row_id'		=>$record['app_row_id'],
		);

		$feed_content = M('FeedData')->where('feed_id='.$record['feed_id'])->getField('feed_content');
		$publish_time = formatTime($record['publish_time']);
		$uname = M('User')->where('uid='.$record['uid'])->getField('nickname');
		$feed_type_name = M('FeedType')->where('tid='.$record['feed_type'])->getField('tname');
		$isExist = M('Collection')->where(array('source_id'=>$record['feed_id'],'uid'=>$_SESSION[C(USER_AUTH_KEY)]))->count();
		
		if($temp['uid'] == $_SESSION[C(USER_AUTH_KEY)])
			$temp['del_display'] = 1;
		else 
			$temp['del_display'] = 0;
		$temp['feed_content'] = $feed_content;
		$temp['publish_time'] = $publish_time;
		$temp['uname'] = $uname;
		$temp['feed_type_name'] = $feed_type_name;
		$temp['isCollected'] = $isExist;
		if($record['type'] == 'repost'){
			$repost = $this->where(array('feed_id'=>$record["app_row_id"]))->find();
			if($repost['is_del'] == 1 || empty($repost))
				$temp['source_feed_del'] = true;
			else{
				$temp['source_feed_del'] = false;
				$temp['source_feed_info'] = $this->formatFeedByRecord($repost);	
			}
		}
		$temp['avatar'] = D('Avatar')->init($record['uid'])->getUserAvatar();
		//过滤掉数目为0的评论,转发,收藏,不显示在页面上
		$record['repost_count']!=0 && $temp['repost_count'] = $record['repost_count'];
		$record['comment_count']!=0 && $temp['comment_count'] = $record['comment_count'];
		$record['collection_count']!=0 && $temp['collection_count'] = $record['collection_count'];
		return $temp;
	}
	/**
	 * 通过feed_id得到feed信息,用于模板输出
	 * @param array  $map  查询条件
	 * @param integer $page 页数,默认为1
	 * @param integer $limit 每页数量,默认为10
	 * @return mix 添加失败返回false，成功返回新的微博ID
	 * @auther icubit
	 */
	public function getFeedByIds($feed_ids){
		$feeds = array();
		if(is_array($feed_ids)){
			foreach($feed_ids as $feed_id){
				if($feed = $this->where(array('feed_id'=>$feed_id,'is_del'=>0))->find()){
					$feeds[] = $this->formatFeedByRecord($feed);
				}
			}
		}
		return $feeds;
	}
	public function getFeedById($feed_id){
		$feed_id = intval($feed_id);
		$feed = $this->where(array('feed_id'=>$feed_id))->find();
		if(empty($feed))
			return false;
		if($feed['is_del'] == 1){
			$return['status'] = 0;
			$return['info'] = '已被作者删除'; 
		}
		$return = $this->formatFeedByRecord($feed);
		$return['status'] = 1;
		return $return;
	}
	/**
	 * 对map数据进行处理
	 * @param array  $map  查询条件
	 * @return mix 返回处理后的$map
	 * @auther icubit
	 */
	public function _escapeMap($map = array()){
		//过滤未知字段
		
		isset($map['cid']) && $map['cid'] = intval($map['cid']);
		isset($map['feed_type']) && $map['feed_type'] = intval($map['feed_type']);
		isset($map['uid']) && $map['uid'] = intval($map['uid']);
		//没设$map['cid']或者$map['cid']=0的话
		if(isset($map['cid']) && $map['cid'] == 0)
			unset($map['cid']);
		if(isset($map['feed_type']) && $map['feed_type'] == 0)
			unset($map['feed_type']);
		if(isset($map['uid']) && $map['uid'] == 0)
			unset($map['uid']);
		
		$map['is_del'] = isset($map['is_del']) ? intval($map['is_del']) : 0;
		
		return $map;
	}
	/* public function getNumOfNew($map = array(),$maxId){
		$map['feed_id']  = array('gt',$maxId);	
		$map['is_del'] = 0;
		$map = $this->_escapeMap($map);
		$count = $this->where($map)->count();
		return $count;
	} */
	public function getCount($map = array()){
		
		$m = $this->_escapeMap($map);
		$count = $this->where($m)->count();
		return $count;
	}
	/**
	 * 得到一次feed,用于模板输出
	 * @param array  $map  查询条件
	 * @param integer $loadId 起始ID,默认为0表示取前limit条记录
	 * @param integer $isAfter 向前或向后,默认向后
	 * @param integer $limit 每次请求数量,默认为10
	 * @return mix 添加失败返回false，成功返回新的微博ID
	 * @auther icubit
	 */
	public function getOnceFeeds($map = array(),$page=1,$limit=10){
		$order = 'publish_time desc,feed_id desc';
		//数据合法性检查
		$map = $this->_escapeMap($map);
		$page = intval($page);
		$limit = intval($limit);
/* 
		if($loadId != 0){
			if($isAfter){
				$map['feed_id']  = array('lt',$loadId);			
			}else{
				$map['feed_id']  = array('gt',$loadId);		
				//$order = 'publish_time asc,feed_id asc';
			}
		} */
		// 数据封装
		$feeds = array();
		$records = $this->where($map)->order($order)->page($page,$limit)->select();
		if(empty($records))
			return false;
		$i=1;
		foreach($records as $record){
			if($i==1){
				$maxId=	$record['feed_id'];
			}
			$minId = $record['feed_id'];
			$feeds[] = $this->formatFeedByRecord($record);
			$i++;
		}
		return array('minId'=>$minId,'maxId'=>$maxId,'data'=>$feeds);

	}
	//添加feed,成功返回feed_id,失败返回false
	public function addFeed($feed,$feed_content){
		$this->create($feed);
		if($feed_id = $this->add()){
			$feedData['feed_id'] = $feed_id;
			$feedData['feed_content'] = $feed_content;
			M('FeedData')->create($feedData);
			if(M('FeedData')->add())
				return $feed_id;
		}
		return false;
	}


	/**
	 * 通过Ids删除feeds
	 * @param $feed_ids ID数组
	 * @param $uid 用户ID,默认为空,取当前用户ID
	 * @return boolean 成功或失败
	 * @auther icubit
	 */
	public function delFeedByIds($feed_ids,$uid=''){
		$feed_ids = array_unique($feed_ids);
		 if(is_array($feed_ids) && !empty($feed_ids)){
			 if(empty($uid))
			 	$uid = $_SESSION[C('USER_AUTH_KEY')];
			 else 
			 	$uid = $intval($uid);
			 foreach($feed_ids as $v){
				 //假删除,feed表仍保留相关信息,feed_data表内容删除
				 /* $u = $this->where(array('feed_id'=>$v))->getField('uid');
				 $u = $this->where(array('feed_id'=>$v))->getField('uid');
				 if($u != $uid){
				 	$this->error = "你无权删除!";
				 } */
				 $this->where(array('feed_id'=>$v,'uid'=>$uid))->setField('is_del',1);
				 M('FeedData')->where(array('feed_id'=>$v,'uid'=>$uid))->delete();
			 }
			 //删除完成后更新用户feed数
			 $num = $this->where(array('uid'=>$uid))->count();
			 D('UserData')->setKeyValue($uid,'feed_count',$num);
			 return true;
		 }
		 return false;
	}

	/**
	 * 获取指定微博的信息
	 * @param integer $feed_id 微博ID
	 * @return mix 获取失败返回false，成功返回微博信息
	 */
	public function get($feed_id) {
		$feed_list = $this->getFeeds(array($feed_id));
		if(!$feed_list) {
			$this->error = L('PUBLIC_INFO_GET_FAIL');			// 获取信息失败
			return false;
		} else {
			return $feed_list[0];
		}
	}

	/**
	 * 获取给定微博ID的微博信息
	 * @param array $feed_ids 微博ID数组
	 * @return array 给定微博ID的微博信息
	 */
	public function getFeeds($feed_ids) {
		$feedlist = array();
		$feed_ids = array_filter(array_unique($feed_ids));

		// 获取数据
		if(count($feed_ids) > 0) {
			$cacheList = model('Cache')->getList('fd_', $feed_ids);
		} else {
			return false;
		}

		// 按照传入ID顺序进行排序
		foreach($feed_ids as $key => $v) {
			if($cacheList[$v]) {
				$feedlist[$key] = $cacheList[$v];
			} else {
				$feed = $this->setFeedCache(array(), $v);
				$feedlist[$key] = $feed[$v];
			}
		}
		return $feedlist;
	}

	/**
	 * 获取指定用户收藏的微博列表，默认为当前登录用户
	 * @param array $map 查询条件
	 * @param integer $limit 结果集数目，默认为10
	 * @param integer $uid 指定用户ID，默认为空
	 * @return array 指定用户收藏的微博列表，默认为当前登录用户
	 */
	public function getCollectionFeed($map, $limit = 10, $uid = '') {
		$map['uid'] = empty($uid) ? $_SESSION['mid'] : $uid;
		$map['source_table_name'] = 'feed';
		$table = "{$this->tablePrefix}collection";
		$feedlist = $this->table($table)->where($map)->field('source_id AS feed_id')->order('source_id DESC')->findPage($limit);
		$feed_ids = getSubByKey($feedlist['data'],'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids);

		return $feedlist;
	}

	/**
	 * 查看指定用户的微博列表
	 * @param array $map 查询条件
	 * @param integer $uid 用户ID
	 * @param string $app 应用类型
	 * @param string $type 微博类型
	 * @param integer $limit 结果集数目，默认为10
	 * @return array 指定用户的微博列表数据
	 */
	public function getUserList($map, $uid, $app, $type, $limit = 10) {
		if(!$uid) {
			$this->error = L('PUBLIC_WRONG_DATA');				// 获取信息失败
			return false;
		}
		!empty($app) && $map['app'] = $app;
		!empty($type) && $map['type'] = $type;
		$map['uid'] = $uid;
		$list = $this->getList($map, $limit);

		return $list;
	}


	/**
	 * 数据库搜索微博
	 * @param string $key 关键字
	 * @param string $type 微博类型，post、repost、postimage、postfile
	 * @param integer $limit 结果集数目
	 * @param boolean $forApi 是否返回API数据，默认为false
	 * @return array 搜索后的微博数据
	 */
/*	public function searchFeeds($key, $feed_type, $limit, $Stime, $Etime) {
		$map['a.is_del'] = 0;
		$map['b.feed_content'] = array('LIKE', '%'.t($key).'%');
		if($feed_type){
			$map['a.type'] = $feed_type;
		}
		if($Stime && $Etime){
			$map['a.publish_time'] = array('between',array($Stime,$Etime));
		}
		$table = "{$this->tablePrefix}feed AS a LEFT JOIN {$this->tablePrefix}feed_data AS b ON a.feed_id = b.feed_id";
		$feedlist = $this->table($table)->field('a.feed_id')->where($map)->order('a.publish_time DESC')->findPage($limit);
		//return D()->getLastSql();exit;
		$feed_ids = getSubByKey($feedlist['data'], 'feed_id');
		$feedlist['data'] = $this->getFeeds($feed_ids);
		foreach($feedlist['data'] as &$v) {
        	switch ( $v['app'] ){
        		case 'weiba':
        			$v['from'] = getFromClient(0 , $v['app'] , '微吧');
        			break;
        		default:
        			$v['from'] = getFromClient( $v['from'] , $v['app']);
        			break;
        	}
        	!isset($uids[$v['uid']]) && $v['uid'] != $GLOBALS['ts']['mid'] && $uids[] = $v['uid'];
        }
		return $feedlist;
	}*/

	/**
	 * 获取最后的错误信息
	 * @return string 最后的错误信息
	 */
	public function getLastError() {
		return $this->_error;
	}
}
?>