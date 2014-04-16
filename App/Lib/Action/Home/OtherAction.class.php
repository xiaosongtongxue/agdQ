<?php
/**
 * OtherAction 其它模块
 * @author  xiaoluo <584188065@qq.com>
 * @version agdQ1.0
 */
 class OtherAction extends Action{
	/**
	 * 关于我们页面
	 */
	public function about(){
		layout('Layout/layout_one');
		//TODO:首页背景图片
		$this->assign('backgroudImages',D('Template')->getBackgroudImageName());
		$this->display();
	}
	
	/**
	 * 用户协议页面
	 */
	public function treaty(){
		layout('Layout/layout_one');
		//TODO:首页背景图片
		$this->assign('backgroudImages',D('Template')->getBackgroudImageName());
		$this->assign('title','用户协议');
		$this->display();
	}
	
	 /**
	  * 云否团队介绍
	  */
/*	public function yunfou(){
		$this->display();
	}*/
	 /**
	  * footer展示
	  */
/*	public function footer(){
		$this->display();
		}*/
}
?>