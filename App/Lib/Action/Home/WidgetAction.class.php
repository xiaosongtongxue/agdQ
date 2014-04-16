<?php

//仿widget写，暂时不知道ajax如何获取widget返回的模板内容
class WidgetAction extends CommonAction{
	/* public function api(){
		$classname = $_POST['widget'].'Widget';
		$method = $_POST['func'].'()';
		import('App.Widget.'.$classname);
		$c = new $classname();
		$exeStr = "\$return = \$c->test();";

		eval($exeStr);

		return $return ;
	} */
	//
	public function comment($feed_id,$page=1,$limit=5){
		$list = D('Comment')->getOnceComments($feed_id,$page,$limit);
		$this->assign('feed_id',$feed_id);
		$this->assign('list',$list);
		$html = $this->fetch();
		$return = array('status'=>1,'info'=>'success','data'=>$html);
		if($this->isAjax()){
			$test=1;
			//return json_encode($return);这种写法有问题，为什么？？
			exit(json_encode($return));
		}else{
			//$test=2;
			return $return['data'];
		}
		//$this->error();
	//	return $this->isAjax() ? json_encode($return) : $return['data'];
	}

	public function addComment(){
		 // 判断资源是否被删除
		$is_del = M('Feed')->where(array('feed_id'=>intval($_POST['feed_id']),'is_del'=>0))->count();
		if(!$is_del)
			exit(json_encode(array('status'=>0,'info'=>'评论失败,资源已删除','data'=>'')));
		if($comment_id = D('Comment')->addComment($_POST)){
			$comment_info = D('Comment')->getCommentById($comment_id);
			$list = array();
			$list[] =  $comment_info;
			$this->assign('list',$list);
			$html = $this->fetch('comment');
			$return = array('status'=>1,'info'=>'success','data'=>$html);
			exit(json_encode($return));
		}
		else
			exit(json_encode(array('status'=>0,'info'=>'评论失败','data'=>'')));

	}

	public function loadMoreFeed(){
		$return = array('status'=>0,'info'=>'没有更多了','data'=>'');
		
		$map['cid'] = isset($_POST['cid']) ? $_POST['cid'] : 0;
		$map['feed_type'] = isset($_POST['feed_type']) ? $_POST['feed_type'] : 0;
		$map['uid'] = isset($_POST['uid']) ? $_POST['uid'] : 0;
		$map['feed_id'] = array('elt',intval($_POST['firstId']));
		
		$page = isset($_POST['page']) ? intval($_POST['curPage']) : 1;
		$part = isset($_POST['part']) ? intval($_POST['part']) : 1;
		$feeds = D('Feed')->getOnceFeeds($map,($page-1)*4+$part);
		if($feeds)	{
			$this->assign('feeds',$feeds['data']);
			$html = $this->fetch('./App/Lib/Widget/FeedList/_FeedList.html');
			//$return['minId'] = $feeds['minId'];
			//$return['maxId'] = $feeds['maxId'];
			$return['status'] = 1;
			$return['info'] = 'success';
			$return['data'] = $html;
		}
		exit(json_encode($return));
	}
	public function getNewCount(){
		
		$map['cid'] = isset($_POST['cid']) ? $_POST['cid'] : 0;
		$map['feed_type'] = isset($_POST['feed_type']) ? $_POST['feed_type'] : 0;
		$map['uid'] = isset($_POST['uid']) ? $_POST['uid'] : 0;
		$map['feed_id'] = array('gt',intval($_POST['maxId']));

		//$return = array('status'=>0);
		
		$numOfNew = D('Feed')->getCount($map);
		//$return['count'] = $numOfNew;
		
		
		/* if($numOfNew>10){
			$return['data'] = '';
			$return['status'] = 2;
		}
		elseif(0<$numOfNew){
			$feeds = D('Feed')->getOnceFeeds(array('cid'=>$cid),$lastId);
			
			$this->assign('feeds',$feeds['data']);
			$html = $this->fetch('./App/Lib/Widget/FeedList/_FeedList.html');
			$return['minId'] = $feeds['minId'];
			$return['maxId'] = $feeds['maxId'];
			$return['status'] = 1;
			$return['info'] = 'success';
			$return['data'] = $html;	
		} */
		exit(json_encode(array('count'=>$numOfNew,'status'=>1))); 
	}
	
	public function loadNew(){
		$return = array('status'=>2);
		$map['cid'] = isset($_POST['cid']) ? $_POST['cid'] : 0;
		$map['feed_type'] = isset($_POST['feed_type']) ? $_POST['feed_type'] : 0;
		$map['uid'] = isset($_POST['uid']) ? $_POST['uid'] : 0;
		$map['feed_id'] = array('gt',intval($_POST['maxId']));
		
		$numOfNew = D('Feed')->getCount($map);
		//如果新feed条数超过15条,则重新加载页面
		if($numOfNew <= 15){
			$feeds = D('Feed')->getOnceFeeds($map,1,15);
			$this->assign('feeds',$feeds['data']);
			$html = $this->fetch('./App/Lib/Widget/FeedList/_FeedList.html');
			$return['status'] = 1;
			$return['maxId'] = $feeds['maxId'];
			$return['info'] = 'success';
			$return['data'] = $html;
		}
		exit(json_encode($return)); 
	}
	
	public function loadMoreByPage(){
		//以下变量必须
		$first_id = intval($_POST['firstId']);
		$page = intval($_POST['page']);
		$last = intval($_POST['last']);
		
		$map['cid'] = isset($_POST['cid']) ? $_POST['cid'] : 0;
		$map['feed_type'] = isset($_POST['feed_type']) ? $_POST['feed_type'] : 0;
		$map['uid'] = isset($_POST['uid']) ? $_POST['uid'] : 0;
		$map['feed_id'] = array('elt',$first_id);
		
		/* $feeds = D('Feed')->getOnceFeeds($map,$page,40);
		$this->assign('feeds',$feeds['data']);
		$html = $this->fetch('./App/Lib/Widget/FeedList/_FeedList.html'); */
		
		$html = $this->getFeedPageHtml($map, $page, 40);
		$content = $this->getPageBarHtml(1, $page, $last);
		
		exit(json_encode(array('status'=>1,'page_html'=>$content,'data'=>$html)));
		//return json_encode(array('status'=>1,'page_html'=>$content,'data'=>$data));
	}
	public function getPageBar(){
		$first = intval($_POST['first']);
		$cur = intval($_POST['cur']);
		$last = intval($_POST['last']);
		
		$html = $this->getPageBarHtml($first, $cur, $last);
		exit(json_encode(array('status'=>1,'data'=>$html)));
		//return json_encode(array('status'=>1,'data'=>$html));
	}
	//得到pageBar的html代码
	private function getPageBarHtml($first,$cur,$last){
		$first = intval($first);
		$cur = intval($cur);
		$last = intval($last);
		
		$page_bar = getPageBar($first, $cur,$last);
		$this->assign('page_bar',$page_bar);
		$html = $this->fetch('./App/Lib/Widget/PageBar/PageBar.html');
		
		return $html;
	}
	//得到$limit条feed的html代码
	public function getFeedPageHtml($map,$page,$limit){
		
		$feeds = D('Feed')->getOnceFeeds($map,$page,$limit);
		$this->assign('feeds',$feeds['data']);
		$html = $this->fetch('./App/Lib/Widget/FeedList/_FeedList.html');
		
		return $html;
	}
	
	//ajax share
	public function share(){
		
		//$type = $_POST['type'];
		//$source_table_name = $_POST['source_table_name'];
		$data = array();
		$feed_id = intval($_REQUEST['sourse_id']);
		$feed = D('Feed')->getFeedById($feed_id);
		if($feed['type'] == 'repost'){
			$data['feed'] = $feed['source_feed_info'];
			$data['textarea_value'] = '//@'.$feed['uname'].': '.$feed['feed_content'];
		} else{
			$data['feed'] = $feed;
			$data['textarea_value'] = '';
		} 
		$this->assign('data',$data);
		/* $feeds = D('Feed')->getOnceFeeds($map,$page,1);
		if($feeds['data']['type'] == 'repost'){
			$feeds['data']['textarea_value'] = $feeds['data']['feed_content'];
		}else{
			$feeds['data']['textarea_value'] = '';
		}
		$this->assign('feeds',$feeds['data']); */
		/* if($this->isAjax()){
			$html = $this->fetch();
			exit(json_encode(array('status'=>1,'data'=>$html)));
		}else{ */
			$this->display();
		//}
	}
}

?>