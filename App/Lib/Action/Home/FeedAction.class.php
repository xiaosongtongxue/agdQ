<?php
/**
 * FeedAction 发表模块
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class FeedAction extends CommonAction{
	private $_feed_model;			// 信息模型字段
	private $data = array(
				'from', 	//客户端类型，0：网站；1：手机网页版；2：android；3：iphone 默认为0
				'content',	//客户端用户发表的信息
				);
	/**
	 * 模块初始化，获取信息模型对象
	 * @return void
	 */
	public  function _initialize(){
		parent::_initialize();
		$this->_feed_model = D('Feed');
	}
	/**
	 * 发布微博操作，用于AJAX
	 * @return json 发布微博后的结果信息JSON数据
	 */
	public function postFeed()
	{
		// 返回数据格式
		$return = array('status'=>1, 'data'=>'' );
		// 用户发送内容
		//if(!i)
		$content = isset($_POST['content']) ? h($_POST['content']) : '';
		$d['cid'] = isset($_POST['cid']) ? intval($_POST['cid']) : 0;
		$d['feed_type'] = isset($_POST['feed_type']) ? intval($_POST['feed_type']) : 1;
		$d['app_row_id'] = isset($_POST['feed_id']) ? intval($_POST['feed_id']) : 0;
		//$d['body'] = preg_replace("/#[\s]*([^#^\s][^#]*[^#^\s])[\s]*#/is",'#'.trim("\${1}").'#',$d['body']);
		// 附件信息
		//$d['attach_id'] = trim(t($_POST['attach_id']), "|");
		//!empty($d['attach_id']) && $d['attach_id'] = explode('|', $d['attach_id']);
		// 发送微博的类型
		$d['type'] = isset($_POST['type']) ? t($_POST['type']) : 'post';

		if($data = D('Feed')->put($d,$content)) {
			
			// 微博来源设置
			$data['from'] = getFromClient($data['from']/*, $data['app']*/);
			
			$this->assign('feeds',array($data));
			$return['data'] = $this->fetch('./App/Lib/Widget/FeedList/_FeedList.html');
			$return['maxId'] = $data['feed_id'];
			// 微博ID
			//$return['feedId'] = $data['feed_id'];
			
			//更新用户最后发表的微博
			$last['last_feed_id'] = $data['feed_id'];
			$last['last_post_time'] = $_SERVER['REQUEST_TIME'];
			D( 'User' )->where('uid='.$this->uid)->save($last);

		} else {
			$return = array('status'=>0,'data'=>D('Feed')->getLastError());
		}

		exit(json_encode($return));
	}
	/**
	 * 分享/转发微博操作，需要传入POST的值
	 * @return json 分享/转发微博后的结果信息JSON数据
	 */
	public function shareFeed()
	{
		// 获取传入的值
		$post = $_POST;
		// 安全过滤
		foreach($post as $key => $val) {
			$post[$key] = t($post[$key]);
		}
		// 判断资源是否删除
		if(empty($post['curid'])) {
			$map['feed_id'] = $post['sid'];
		} else {
			$map['feed_id'] = $post['curid'];
		}
		$map['is_del'] = 0;
		$isExist = model('Feed')->where($map)->count();
		if($isExist == 0) {
			$return['status'] = 0;
			$return['data'] = '内容已被删除，转发失败';
			exit(json_encode($return));
		}
		// 过滤内容值
		$content = isset($_POST['content']) ? h($_POST['content']) : '';
		//$post['body'] = h($post['body']);
		// 进行分享操作
		$return = model('Share')->shareFeed($post, 'share');
		if($return['status'] == 1) {
			// 添加积分
			model('Credit')->updateUserCredit($this->uid,'weibo_share');
			$this->assign($return['data']);
			$return['data'] =  $this->fetch('PostFeed');
		}
		exit(json_encode($return));
	}
	
	//ajax删除feed_id
	public function delFeed(){
		$feed_ids = array();
		$feed_ids[] = intval($_POST['feed_id']); 
		if(D('Feed')->delFeedByIds($feed_ids))
			$return  = array('status'=>1,'info'=>'success');
		else 
			$return = array('status'=>0,'info'=>'fail');
		exit(json_encode($return));
		
	}
	
}
?>