<?php
/**
 * 个人主页设置控制器
 * @author xiaoluo <584188065@qq.com>
 * @version agdQ 1.0
 */
class PersonalHomepageAction extends CommonAction{
	/**
	 * 个人主页页面
	 *@param $uid 显示$uid的个人主页
	 */
	public function personalHomepage(){
		$uid = intval($_REQUEST['uid']);
		empty($uid) && $uid = $_SESSION [C('USER_AUTH_KEY')];
		
		$this->assign('uid', $uid);
		
		$name = '某人';
		$this->setTitle($name.'的个人主页');
		$this->display();
	}
}
?>