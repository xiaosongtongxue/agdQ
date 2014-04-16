<?php
class CommentAction extends CommonAction{

	public function comment(){
		$feed_id = intval($_POST['feed_id']);
		//$page = intval($_POST['page']);
		$content = $_POST['content'];
		if($comment_id = D('Comment')->addComment($feed_id,$content)){
			$comment = D('Comment')->getCommentInfo($comment_id);
			$this->ajaxReturn($comment,'success',1);
		}
		$this->error('添加评论失败');

	}
	public function getCommentList(){
		$feed_id = $_POST['feed_id'];
		if($comments = D('Comment')->getOnceComments($feed_id,1,3))
			$this->ajaxReturn($comments,'success',1);
		else
			$this->error('获取失败');
	}
	

}
?>