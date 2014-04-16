<?php
class ConfigAction extends AdministratorAction {
	/**
	 * 系统配置 - 站点配置
	 */
	public function siteConfig() {
		$SystemConfig = D('SystemConfig');
		$defaultLoginBG = $SystemConfig->getSystemConfig('DEFAULT_LOGIN_BG');
		$this->assign('defaultLoginBG',$defaultLoginBG);
		$this->display();
	}
	
	//注意：数组键名必须和数据库中要更新的key一致
	public function saveSiteConfig(){
		$arr['DEFAULT_LOGIN_BG'] = $_POST['defaultLoginBG'];
		/*dump($arr);
		exit();*/
		$SystemConfig = D('SystemConfig');
		if($SystemConfig->saveSiteConfig($arr))
			$this->success('保存成功');
		else
			$this->error('保存失败');
	}
}

?>