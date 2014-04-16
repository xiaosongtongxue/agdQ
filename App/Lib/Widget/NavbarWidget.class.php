<?php
class NavbarWidget extends Widget{
	public function render($data){
		$var['navbar'] = C('NAVBAR');
		$var['user_info'] = D('User')->getUserInfo($data['uid']);
		$var['unread'] = D('UserCount')->getUnreadCount();
		$content = $this->renderFile('Navbar', $var);
		unset($var, $data);
		// 输出数据
		return $content;
	}
}
?>