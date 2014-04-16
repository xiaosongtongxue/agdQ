<?php
class PostFeedWidget extends Widget{
	public function render($data){
		$var['cid'] = $data['cid'];
		$var['cname'] = M('Circle')->where(array('cid'=>$data['cid']))->getField('cname');
		$var['feed_type'] = M('feed_type')->select();
		$content = $this->renderFile('PostFeed', $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>