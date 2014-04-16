<?php
class UserAction extends CommonAction{
//已迁移到AccountAction.class.php文件里面，该文件专门处理账号体系，这样以后好处理。UserAction这个类范围有点大，比如用户不光有账号，还有消息，等等其他的东西

	//用户个人空间
	public function userProfile($uid){
		$this->setTitle('用户中心')->display();
	}
}
?>