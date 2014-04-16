<?php
/**
 * 评论模型 - 数据对象模型
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class CommentModel extends Model {
	/* protected $_auto = array (
			//array('status','1'),  // 新增的时候把status字段设置为1
			//array('password','md5',1,'function') , // 对password字段在新增的时候使md5函数处理
			//array('name','getName',1,'callback'), // 对name字段在新增的时候回调getName方法
			array('ctime','time',3,'function'), // 对create_time字段在更新的时候写入当前时间戳
	); */
	public function getOnceComments($feed_id,$page=1,$limit=10){
		// 数据封装
		$comments = array();
		$condition['feed_id'] = $feed_id;
		$condition['is_del'] = 0;
		$records = $this->field('comment_id,feed_id,feed_uid,uid,content,to_comment_id,to_uid,ctime')->page($page.','.$limit)->where($condition)->order('ctime desc')->select();
		foreach ($records as $record){
			$record['uname'] = M('User')->where('uid='.$record['uid'])->getField('nickname');

			$record['ctime'] = formatTime($record['ctime']);
			$record['avatar'] = D('Avatar')->init($record['uid'])->getUserAvatar();
			$comments[] = $record;
		}
		return $comments;
	}

    /**
     * 添加评论操作
     * @param array $data 评论数据
     * @param boolean $forApi 是否用于API，默认为false
     * @param boolean $notCount 是否统计到未读评论
     * @param array $lessUids 除去@用户ID
     * @return boolean 返回添加评论ID
     */
     public function addComment($data) {
		//下面这些数据是必须的
		if(!isset($data['feed_id']) || !isset($data['content']) || !isset($data['to_comment_id']) )
		{
			return false;
		}
    	$add['feed_id'] = intval($data['feed_id']);
		$add['content'] = preg_html($data['content']);
		$add['to_comment_id'] = intval($data['to_comment_id']);

		$add['feed_uid'] = M('Feed')->where(array('feed_id'=>$add['feed_id']))->getField('uid');
		$add['to_uid'] = M('Comment')->where(array('comment_id'=>$add['to_comment_id']))->getField('uid');
		$add['uid'] = $_SESSION[C('USER_AUTH_KEY')];

		$add['ctime'] = $_SERVER['REQUEST_TIME'];
		//像内容为   "回复@icubit:XXXXX" to_uid=icubit
		//$add = $this->_escapeData($data);
		if($add['content'] === '') {
			$this->error = L('PUBLIC_COMMENT_CONTENT_REQUIRED');        // 评论内容不可为空
			return false;
		}
		if($comment_id = $this->add($add)){
			//feed表评论数加1
			D('Feed')->where(array('feed_id'=>$add['feed_id']))->setInc('comment_count',1);
			//发送评论通知消息
			$lessUids = array();
			$extra_uids = array();
			$lessUids[] = $_SESSION[C('USER_AUTH_KEY')];
			$extra_uids[] = $add['feed_uid'];
			D('Atme')->setAppName('Public')->setAppTable('comment')->addAtme($add['content'], $comment_id, $extra_uids, $lessUids);
			return $comment_id;
		}

		return false;
    }
    /**
     * 检测数据安全性
     * @param array $data 待检测的数据
     * @return array 验证后的数据
     */
    private function _escapeData($data) {
    	//$add['app'] = !$data['app'] ? $this->_app : $data['app'];
    	//$add['table'] = !$data['table'] ? $this->_app_table : $data['table'];
    	$add['row_id'] = intval($data['row_id']);
    	$add['app_uid'] = intval($data['app_uid']);
    	$add['uid'] = $_SESSION[C('USER_AUTH_KEY')];
    	$add['content'] = preg_html($data['content']);
    	$add['to_comment_id'] = intval($data['to_comment_id']);
    	$add['to_uid'] = intval($data['to_uid']);
    	$add['data'] = serialize($data['data']);
    	$add['ctime'] = $_SERVER['REQUEST_TIME'];
    	$add['client_type'] = isset($data['client_type']) ? intval($data['client_type']) : getVisitorClient();

    	return $add;
    }
     /**
     * 添加评论操作
     * @param array $data 评论数据
     * @param boolean $forApi 是否用于API，默认为false
     * @param boolean $notCount 是否统计到未读评论
     * @param array $lessUids 除去@用户ID
     * @return boolean 是否添加评论成功
     */
   /*  public function addComment($data, $forApi = false, $notCount = false, $lessUids = null,$extra_uids = null) {
    	// 判断用户是否登录
    	if(!$_SESSION[C('USER_AUTH_KEY')]){
    		$this->error = L('PUBLIC_REGISTER_REQUIRED');         // 请先登录
    		return false;
    	}
    	// 设置评论绝对楼层
    	//$data['data']['storey'] = $this->getStorey($data['row_id'], $data['app'], $data['table']);
    	// 检测数据安全性
    	$add = $this->_escapeData($data);
    	if($add['content'] === '') {
    		$this->error = L('PUBLIC_COMMENT_CONTENT_REQUIRED');        // 评论内容不可为空
    		return false;
    	}
    	$add['is_del'] = 0;

    	if($res = $this->add($add)) {
    		// 获取排除@用户ID
    		//$lessUids[] = intval($data['app_uid']);
    		$extra_uids = intval($data['app_uid']);
    		$lessUids[] = intval($data['uid']);
    		!empty($data['to_uid']) && $extra_uids[] = intval($data['to_uid']);
    		// 获取用户发送的内容，仅仅以//进行分割
    		$scream = explode('//', $data['content']);
    		D('Atme')->setAppName('Public')->setAppTable('comment')->addAtme(trim($scream[0]), $res, $extra_uids, $lessUids);
    		// 被评论内容的“评论统计数”加1，同时可检测出app，table，row_id的有效性
    		$pk = D($add['table'])->getPk();
    		D($add['table'])->setInc('comment_count', "`{$pk}`={$add['row_id']}", 1);
    		D($add['table'])->setInc('comment_all_count', "`{$pk}`={$add['row_id']}", 1);
    		// 给应用UID添加一个未读的评论数 原作者
    		if($_SESSION[C('USER_AUTH_KEY')] != $add['app_uid'] && $add['app_uid'] != '') {
    			!$notCount && D('UserData')->updateKey('unread_comment', 1, true, $add['app_uid']);
    		}
    		// 回复发送提示信息
    		if(!empty($add['to_uid']) && $add['to_uid'] != $_SESSION[C('USER_AUTH_KEY')]) {
    			!$notCount && model('UserData')->updateKey('unread_comment', 1, true, $add['to_uid']);
    		}
    		// 加积分操作
    		if($add['table'] =='feed'){
    			model('Credit')->updateUserCredit($GLOBALS['ts']['mid'], 'weibo_reply');
    			model('Feed')->cleanCache($add['row_id']);
    		}
    		// 发邮件
    		if($add['to_uid'] != $GLOBALS['ts']['mid'] || $add['app_uid'] != $GLOBALS['ts']['mid'] && $add['app_uid'] != '') {
    			$author = model('User')->getUserInfo($GLOBALS['ts']['mid']);
    			$config['name'] = $author['uname'];
    			$config['space_url'] = $author['space_url'];
    			$config['face'] = $author['avatar_middle'];
    			$sourceInfo = model('Source')->getSourceInfo($add['table'], $add['row_id'], $forApi, $add['app']);
    			$config['content'] = parse_html($add['content']);
    			$config['ctime'] = date('Y-m-d H:i:s',time());
    			$config['sourceurl'] = $sourceInfo['source_url'];
    			$config['source_content'] = parse_html($sourceInfo['source_content']);
    			$config['source_ctime'] = date('Y-m-d H:i:s',$sourceInfo['ctime']);
    			if(!empty($add['to_uid'])) {
    				// 回复
    				$config['comment_type'] = '回复 我 的评论:';
    				model('Notify')->sendNotify($add['to_uid'], 'comment', $config);

    			} else {
    				// 评论
    				$config['comment_type'] = '评论 我 的微博:';
    				if(!empty($add['app_uid'])) {
    					model('Notify')->sendNotify($add['app_uid'], 'comment', $config);
    				}
    			}
    		}
    	}

    	$this->error = $res ? L('PUBLIC_CONCENT_IS_OK') : L('PUBLIC_CONCENT_IS_ERROR');         // 评论成功，评论失败

    	return $res;
    } */

    /*
	 *通过ID得到comment信息
	 *@param integer $comment_id 评论ID
	 *@param boolean $getSource 是否得到关联feed的相关信息，默认不得到
	 *@return 
	 *@auther icubit
	 */
    public function getCommentById($comment_id,$getSource = false){
    	$comment = $this->field('comment_id,feed_id,feed_uid,uid,content,to_comment_id,to_uid,ctime')->where(array('comment_id'=>$comment_id))->find();
    	$comment['uname'] = M('User')->where('uid='.$comment['uid'])->getField('nickname');
    	$comment['ctime'] = formatTime($comment['ctime']);
		if($getSource){
			
			$comment['sourceInfo'] = D('Feed')->getFeedById($comment['feed_id']);	
		}
    	return $comment;
    }
}
?>