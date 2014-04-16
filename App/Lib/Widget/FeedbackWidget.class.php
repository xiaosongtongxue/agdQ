<?php
   /**
	* 反馈组件
	* @example <{:W('Feedback',array('uid'=>'[登录用户ID]'))}>
	* @version agdQ 1.0
	*/
class FeedbackWidget extends Widget {
	/**
	 *@param $uid 登录用户的ID
     */
	public function render($data) {
		$var['uid'] = isset($data['uid']) ? $data['uid'] : $_SESSION[C(USER_AUTH_KEY)] ;
		$content = $this->renderFile('Feedback', $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>