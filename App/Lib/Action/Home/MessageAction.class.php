<?php
/*
 * 消息模块
 * @author icubit
 */
class MessageAction extends CommonAction{
	private $leftNav = array(
			'atme'=>array('menu'=>'atme',       	'name'=>'@我的', 'href'=>'__URL__/atme'),
			'sysNotify'=>array('menu'=>'sysNotify',  'name'=>'系统消息', 'href'=>'__URL__/sysNotify'),
			'comment'=>array('menu'=>'comment', 	'name'=>'评论', 'href'=>'__URL__/comment'),
			'333'=>array('menu'=>'domain',         'name'=>'个性域名', 'href'=>'__URL__/domain'),
			'at44me'=>array('menu'=>'privacySetting', 'name'=>'隐私设置', 'href'=>'__URL__/privacySetting'),
			'at444me'=>array('menu'=>'notifySetting',  'name'=>'通知设置', 'href'=>'__URL__/notifySetting'),
			'at4me'=>array('menu'=>'blacklist',      'name'=>'黑名单',   'href'=>'__URL__/blacklist'),
	);
	public function _initialize(){
		parent::_initialize();
		$this->assign('menulist',$this->leftNav);
			
	}
	public function setCurMenu($menu){
		$this->assign('menu',$menu);
		$this->assign('title',$this->leftNav[$menu]['name']);
		return $this;
	}
	
	public function index(){
		redirect(U('atme'));	
	}
	//显示@me页面
	public function atme(){
		//获取未读@Me的条数
		$this->assign('unread_atme_count',D('UserData')->where('uid='.$this->uid." and `key`='unread_atme'")->getField('value'));
		// 拼装查询条件
		$map['uid'] = $this->uid;
		
		// 获取@Me微博列表
		$at_list = D('Atme')->getAtmeList($map);
		$this->assign('at_list',$at_list);
		$this->setCurMenu('atme')->display();
	}
	//显示系统消息页面
	public function sysNotify(){
		$this->setCurMenu('sysNotify')->display();
	}
	//显示消息评论页面
	public function comment(){
		$this->setCurMenu('comment')->display();
	}
}
?>