<?php
/**
 * FeedbackAction 意见反馈模块
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
class FeedbackAction extends CommonAction{
				
	/**
	 * 模块初始化，获取信息模型对象
	 * @return void
	 */
/*	public  function _initialize(){
		parent::_initialize();
		$this->_feed_model = D('Feed');
	}*/
	
	/**
	 * 发布微博操作，用于AJAX
	 * @return json 发布微博后的结果信息JSON数据
	 */
	public function feedback(){
		$this->display();
	}	
}
?>