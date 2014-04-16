<?php
class IndexAction extends AdministratorAction{
	public function _initialize(){
		parent::_initialize();
		$this->setCurMenu('User');
	}
	public function index(){
		//$this->show('首页');
		//$this->display();
		redirect(U('RBAC/user'));
	}
}
?>