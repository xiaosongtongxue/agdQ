<?php
class UserInfoWidget extends Widget{
	public function render($data){
		$var['uid'] = $data['uid'];
		$var['user'] = D('User')->getUserInfo($var['uid']);
		
		$content = $this->renderFile('UserInfo', $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>