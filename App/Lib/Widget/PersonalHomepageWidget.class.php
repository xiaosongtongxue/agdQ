<?php
   /**
	* 个人主页相关组件
	* @example <{:W('PersonalHomepage',array('file_html'=>'myHomepage | personalData | personalPhoto | personalActivity | '[待定]'','uid'=>'['用户ID']'))}>
	* @version agdQ 1.0
	*/
class PersonalHomepageWidget extends Widget {
	/**
     *@param file_html 组件模板文件名称 有singleActivity 和Activity可选择
	 *@param uid 活动的ID
     */
	public function render($data) {
		// 个人主页的用户ID
		empty($data['uid']) && $uid = $data['uid'];
		$fileName = isset($data['file_html']) ? $data['file_html'] : 'Activity';
		
		if($fileName == 'myHomepage'){
			// TODO:本人主页需要的相关信息
		}else if($fileName == 'personalData'){
			// TODO:个人资料需要的相关信息
		}else if($fileName == 'personalPhoto'){
			// TODO:个人相册需要的相关信息
		}else if($fileName == 'personalActivity'){
			// TODO:个人活动需要的相关信息
		}
		
		$content = $this->renderFile($fileName, $var);
		unset($var, $data);
        // 输出数据
		return $content;
	}
}
?>